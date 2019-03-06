import { Base } from '../../utils/base.js'

class My extends Base {
  constructor() {
    super();
  }

  //得到用户信息
  getUserInfo(cb) {
    var that = this;
    wx.login({
      success: function () {
        wx.getUserInfo({
          success: function (res) {
            typeof cb == "function" && cb(res.userInfo);
            //将用户昵称 提交到服务器
              that._updateUserInfo(res);
          },
          fail: function (res) {
            wx.authorize({
              scope: 'scope.userInfo',
              success() {
                wx.startRecord()
              }
            })
          }
        });
      },

    })
  }

  /*更新用户信息到服务器*/
  _updateUserInfo(res) {
    var token = wx.getStorageSync('token');
    var allParams = {
      url: 'token/info',
      data: { token: token, 
              data:res
           },
      type: 'post',
      sCallback: function (data) {
      }
    };
    this.request(allParams);

  }
}



export { My }