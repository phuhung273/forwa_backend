
class InMemoryUserStore {
  constructor() {
    this.users = [];
  }
  
  findUserBySessionId(sessionID) {
    return this.users.find(item => item.sessionID === sessionID);
  }

  addUser(user) {
    this.users.push(user);
  }
  
  saveUser(userID, user) {
    this.users = this.users.map(item => item.userID === userID ? user : item);
  }

  deleteUser(userID) {
    this.users = this.users.filter(item => item.userID !== userID);
  }
  
  findAllUsers() {
    return this.users;
  }
}
  
module.exports = {
  InMemoryUserStore
};