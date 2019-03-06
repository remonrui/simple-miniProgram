// pages/detail/detail.js
import { Detail } from 'detail-model.js';

var detail = new Detail();  //实例化详情对象

Page({

  /**
   * 页面的初始数据
   */
  data: {
    loadingHidden: false,
    message: false,
    detail:{},
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (option) {
    var id = option.id;
    this.data.id = id;
    this._loadData();
    this._like()
  },

  /*加载所有数据*/
  _loadData: function (callback) {
    var that = this;
    detail.getDetailInfo(this.data.id, (data) => {
      detail.setTitle(data);
      that.setData({
        detail: data,
        loadingHidden: true
      });
      callback && callback();
    });
  },

  //是否收藏
  _like: function (callback)
  {
    var that = this;
    detail.getLike(this.data.id, (data) => {
      that.setData({
        like: data
      });
      callback && callback();
    });
  },

  //进行收藏或取消
  changLike: function (callback)
  {
    var that = this;
    detail.changeLike(this.data.id,(data) => {
      that.setData({
        like:data
      });
    });
  },

  //是否收藏
  message: function () {
    var that = this;
      that.setData({
        message: true
      });
  },

  //发送留言
  send: function (event) {
    var that = this;
    var msg = event.detail.value.message;
    var id = this.data.id;
    detail.message(id, msg, (data) => {
      if(data == 1){
        wx.showToast({
          title: '留言成功',
          image: '../../imgs/icon/success.png',
          duration: 2000
        });
      }else{
        wx.showToast({
          title: '出了点意外',
          image: '../../imgs/icon/error.png',
          duration: 2000
        });
      }
    });
    that.setData({
      message: false
    });
  },

  //取消留言
  cancel: function () {
    var that = this;
    that.setData({
      message: false
    });
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
    this._loadData(() => {
      wx.stopPullDownRefresh()
    });
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },
  //分享效果
  onShareAppMessage: function () {
    return {
      title: '业务详情',
      path: 'pages/detail/detail'
    }
  }
})