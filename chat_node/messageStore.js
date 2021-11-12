  
/* abstract */ class MessageStore {
  saveMessage(message) {}
  findMessagesForUser(userID) {}
}
  
class InMemoryMessageStore extends MessageStore {
  constructor() {
    super();
    this.messages = [];
  }
  
  saveMessage(message) {
    this.messages.push(message);
  }
  
  findMessagesForUser(userID) {
    return this.messages.filter(
      ({ fromID, toID }) => fromID === userID || toID === userID
    );
  }
}
  
module.exports = {
  InMemoryMessageStore,
};