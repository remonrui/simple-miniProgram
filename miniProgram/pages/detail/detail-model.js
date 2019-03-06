/**
 * Created by jimmy on 17/2/26.
 */

import { Base } from '../../utils/base.js';

class Detail extends Base {
  constructor() {
    super();
  }

  //文章详细信息
  getDetailInfo(id, callback) {
    var param = {
      url: 'category/detail?id=' + id,
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }

  //文章收藏
  getLike(id, callback) {
    var param = {
      url: 'category/like?id=' + id,
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }

  //改变收藏
  changeLike(id, callback) {
    var param = {
      url: 'category/changeLike?id=' + id,
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }

  //留言
  message(id, msg,callback) {
    var param = {
      url: 'category/message?id='+ id + '&msg=' + msg,
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
  
  setTitle(data){
  wx.setNavigationBarTitle({
    title: data.title
    })
  }
};

export { Detail }
