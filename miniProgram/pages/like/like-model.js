import { Base } from '../../utils/base.js';

class Like extends Base {
  constructor() {
    super();
  }

  /*获得所有收藏*/
  getAllLikes(callback) {
    var param = {
      url: 'my/like',
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
}

export { Like };