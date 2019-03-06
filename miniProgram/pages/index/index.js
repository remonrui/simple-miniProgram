import {Index} from 'index-model.js';

var index = new Index();

Page({
  data: {
    bannerArr :{},
    loadingHidden: false
  },

  onLoad: function () {
    this._loadData();
  },

  _loadData: function(callback){
    var that = this;

    // 获得bannar信息
    index.getBannerData((data) => {
      that.setData({
        bannerArr: data,
      });
    });

    /*获取案例信息*/
    index.getCaseData((data) => {
      that.setData({
        caseArr: data,
        loadingHidden: true
      });
    });
    
  },

  imgPreview: function (event) {
    var index = event.currentTarget.dataset.index;
    var imgs = new Array;
    for (var i = 0; i < this.data.caseArr.length; ++i) {
      imgs[i] = this.data.caseArr[i].img;     
    }
    wx.previewImage({
      current: imgs[index], // 当前显示图片的http链接
      urls: imgs // 需要预览的图片http链接列表

    })
  },

  //分享效果
  onShareAppMessage: function () {
    return {
      title: '首页',
      path: 'pages/index/index'
    }
  }
})
