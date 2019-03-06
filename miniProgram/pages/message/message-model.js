import { Base } from '../../utils/base.js';

class Message extends Base {
  constructor() {
    super();
  }

  /*获得所有收藏*/
  getAllMessage(callback) {
    var param = {
      url: 'my/message',
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
}

export { Message };