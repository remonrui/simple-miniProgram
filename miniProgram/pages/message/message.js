// pages/like/like.js
import { Message } from '../message/message-model.js';

var message = new Message();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loadingHidden: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadData();
    for (var i in this.data.messages) {
      this.data.messages[i].show = false; // 添加新属性
    };
  },

  _loadData: function () {
    var that = this;
    message.getAllMessage((data) => {
      that.setData({
        messages: data,
        loadingHidden: true
      });
    });
  },

  //回复隐藏或展示
  messageShow(event){
    var that = this;
    var index = message.getDataSet(event,'index');
    var key = "messages["+ index + "].show";
    var val = this.data.messages[index].show;
    that.setData({
      [key]: !val
    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },
})