import { Base } from '../../utils/base.js';

class Category extends Base {
  constructor() {
    super();
  }

  /*获得所有主分类*/
  getCategoryType(callback) {
    var param = {
      url: 'category/main',
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
  /*获得某主分类的子分类*/
  getCategorySon(id, callback) {
    var param = {
      url: 'category/son?id=' + id,
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
}

export { Category };