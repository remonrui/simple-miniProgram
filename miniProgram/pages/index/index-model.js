import { Base } from '../../utils/base.js';

class Index extends Base {
  constructor() {
    super();
  }

  /*banner图片信息*/
  getBannerData(callback) {
    var that = this;
    var param = {
      url: 'carousel/all',
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }

  /*案例*/
  getCaseData(callback) {
    var param = {
      url: 'cases/all',
      sCallback: function (data) {
        callback && callback(data);
      }
    };
    this.request(param);
  }
};

export { Index }; 