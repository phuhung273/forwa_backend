require('dotenv').config();

const httpServer = require('http').createServer();
const io = require('socket.io')(httpServer, {
  cors: {
    origin: 'http://localhost:8080',
  },
});

const fs = require('fs');

const crypto = require('crypto');
const randomId = () => crypto.randomBytes(8).toString('hex');

const { InMemoryUserStore } = require('./userStore');
const userStore = new InMemoryUserStore();

const { InMemoryMessageStore } = require('./messageStore');
const messageStore = new InMemoryMessageStore();

const CONNECTED = 1;
const DISCONNECTED = 0;

const TYPE_IMAGE = 'IMAGE';
const TYPE_STRING = 'STRING';
const TYPE_LOCATION = 'LOCATION';

io.use((socket, next) => {
  const sessionID = socket.handshake.auth.sessionID;
  if (sessionID) {
    const user = userStore.findUserBySessionId(sessionID);
    if (user) {
      socket.sessionID = sessionID;
      socket.userID = user.userID;
      socket.username = user.username;
      return next();
    }
  }
  const username = socket.handshake.auth.username;
  const userID = socket.handshake.auth.userID;
  const token = socket.handshake.auth.token;
  if (!username || !userID || !token) {
    
    return next(new Error('invalid credential'));
  }
  socket.sessionID = randomId();
  socket.userID = userID;
  socket.username = username;
  next();
});

io.on('connection', (socket) => {
  // persist session
  userStore.saveUser(socket.userID, {
    userID: socket.userID,
    sessionID: socket.sessionID,
    username: socket.username,
    connected: CONNECTED,
  });

  // emit session details
  socket.emit('session', {
    sessionID: socket.sessionID,
    userID: socket.userID,
  });

  // join the "userID" room
  socket.join(socket.userID);

  // fetch existing users
  let users = userStore.findAllUsers();
  const messagesPerUser = new Map();
  messageStore.findMessagesForUser(socket.userID).forEach((message) => {
    const { fromID, toID } = message;
    const otherUser = socket.userID === fromID ? toID : fromID;
    if (messagesPerUser.has(otherUser)) {
      messagesPerUser.get(otherUser).push(message);
    } else {
      messagesPerUser.set(otherUser, [message]);
    }
  });
  users = users.map((user) => {
    return {
      ...user,
      messages: messagesPerUser.get(user.userID) || []
    };
  });
  socket.emit('users', {users});

  // notify existing users
  socket.broadcast.emit('user connected', {
    userID: socket.userID,
    username: socket.username,
    connected: CONNECTED,
    messages: [],
  });

  // forward the private message to the right recipient (and to other tabs of the sender)
  socket.on('private message', ({ id, content, toID, type }, response) => {
    const message = {
      id,
      fromID: socket.userID,
      toID,
      type,
    };

    if(type === TYPE_IMAGE){
      let filePath = saveBase64(content);
      if (filePath) {
        message.content = filePath;
        socket.to(toID).to(socket.userID).emit('private message', message);
        messageStore.saveMessage(message);
      }
    }else if(type === TYPE_STRING || type === TYPE_LOCATION){
      message.content = content;
      socket.to(toID).to(socket.userID).emit('private message', message);
      messageStore.saveMessage(message);
    }

    response(message);
  });

  // notify users upon disconnection
  socket.on('disconnect', async () => {
    const matchingSockets = await io.in(socket.userID).allSockets();
    const isDisconnected = matchingSockets.size === 0;
    if (isDisconnected) {
      // notify other users
      socket.broadcast.emit('user disconnected', {userID: socket.userID});
      // update the connection status of the session
      userStore.saveUser(socket.userID, {
        userID: socket.userID,
        sessionID: socket.sessionID,
        username: socket.username,
        connected: DISCONNECTED,
      });
    }
  });
});

const PORT = process.env.PORT || 3000;

httpServer.listen(PORT, () =>
  console.log(`server listening at *:${PORT}`)
);

function saveBase64(data) {
  var matches = data.match(/^data:([A-Za-z-+/]+);base64,(.+)$/);
 
  if(matches.length !== 3) {
    return null;
  }
 
  let fileName = Date.now()+'.png';
  try {
    fs.writeFileSync('./public/' + fileName, matches[2], {encoding: 'base64'});
    return fileName;
  } catch (e) {
    console.log(e.message);
    return null;
  }
}