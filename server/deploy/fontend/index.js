// 实现刷新时间
var datetime = new Vue({
  el: '.datetimebox',
  data: {
    hour: "13",
    minutes: "12",
    weeklist: ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
    week: 1,
    month: 5,
    day: 3
  },
  methods: {
      gettime: function(){
        let date = new Date();
        let hour = date.getHours().toString();
        hour = hour.length < 2?("0"+hour):hour;
        let minutes = date.getMinutes().toString();
        minutes = minutes.length < 2?("0"+minutes):minutes;
        let month = (date.getMonth()+1).toString();
        month = month.length < 2?("0"+month):month;
        let day = date.getDate().toString();
        day = day.length < 2?("0"+day):day;
        let week = date.getDay();

        this.hour = hour;
        this.minutes = minutes;
        // console.log(month);
        this.month = month;
        this.day = day;
        this.week = week;
    },
    nowtime: function(){
        setInterval(this.gettime,1000);
    },
    updatedata: function(){
      axios.get('/deploy/backend/updatedata.php')
      .then(function (res) {
          console.log(res);
      });
    },
    autoupdate: function(){
      //  更新图书馆数据
      setInterval(this.updatedata,60000);      
  
    }
  },
  created() {
    this.nowtime();
  },
  // 挂载完成时
  mounted(){
    this.nowtime();
    // this.autoupdate();
  },
})

var library = new Vue({
    el:".data",
    data: {
        // librarylist: [{"deviceName":"\u6c5f\u95e8\u5e02\r\n","device_id":"1"},
        // {"deviceName":"\u6c5f\u95e8\u5e02\r\n","device_id":"2"},
        // {"deviceName":"\u6c5f\u95e8\u5e02\r\n","device_id":"3"}],
        // [{"device_id":"1","deviceName":"\u6c5f\u95e8\u5e02\r\n","date":"2019-05-31","Fin":"20","Fout":"9","updateTime":"2019-05-31 19:39:37"}]
        // librarydata: [{"device_id":"1","deviceName":"\u6c5f\u95e8\u5e02\r\n","date":"2019-05-31","Fin":"15","Fout":"9","updateTime":"2019-05-31 19:39:37"},
        // {"device_id":"2","deviceName":"\u6c5f\u95e8\u5e02\r\n","date":"2019-05-31","Fin":"20","Fout":"9","updateTime":"2019-05-31 19:39:37"},
        // {"device_id":"3","deviceName":"\u6c5f\u95e8\u5e02\r\n","date":"2019-05-31","Fin":"20","Fout":"9","updateTime":"2019-05-31 19:39:37"}],
        librarydata: [],
        // showinglib_index: 0,
        libraryClass: "library",
        choosedClass: "choosed",
        nochoosedClass: "nochoosed",
        show: true

    },
    methods: {
        getlibrarydata: function(){
            // console.log(123);
            var that = this;
            axios.get('/deploy/backend/devicesJoinRidership.php')
            .then(function (res) {
                // console.log(res);
                if (res.data){
                  if (that.library){
                    // 原来已经有数据的场合 更新数据
                    for (let j in res.data){
                      for (let i in that.librarydata){
                        if (res.data[j].device_id == that.librarydata[i].device_id){
                          // 不要忘了赋值动画实现属性
                          that.librarydata[i] = res.data[j];
                        }
                      }
                    }
   
                  }else{
                    that.librarydata = res.data;
                    // document.getElementById("title").innerHTML = that.librarydata[that.showinglib_index].deviceName.replace("/(\s+)|(\n+)/g", "") ;
                    console.log(that.librarydata);
                    // 为每个项目添加属性，用于后续实现动画效果
                    // for (let i in that.librarydata){
                    //   that.librarydata[i].show = true;
                    // }
                    console.log(that.librarydata);
                  }
                  // console.log(that.librarydata[0].Fin);
                  // 图
         
                  dataimg.drawimg(res.data);
                }else{
                  console.log("librarydata null");
                }

            })
            .catch(function (error) {
                console.log(error);
            });
        },
        fadeAction: function(){
          // 列表数量大于一定阈值开启动画播报
          let maxlength = 8;
          var that = this;
          if (that.librarydata.length >= maxlength){
            // 消失动画
            // 列表中实际的最后一项也要消失 并且要弹出
            let item = that.librarydata.pop(); 
            // 添加到列表的开头，并且加入动画
            that.librarydata.unshift(item);
            
          }
        },
        test: function(){
          this.show = false;
        },
        choosing: function(id){
          // 此函数已经不用了 由于需求修改  
          // console.log(id)
            var that = this;
            // console.log(this.librarydata);

            axios.get('/deploy/backend/device_api.php', {
                params: {
                    device_id: id
                }
              })
              .then(function (response) {
                for (i in that.librarydata){
                    if (that.librarydata[i].device_id == id){
                        that.librarydata[i] = response.data;
                        // that.showinglib_index = i;
                        // document.getElementById("title").innerHTML = that.librarydata[i].deviceName.replace("↵", "");
                        // that.showdataimg(that.showinglib_index);
                        break;
                    }
                    // console.log(response.data);
                }
              })
              .catch(function (error) {
                console.log(error);
            });
          },
          titleslice: function(text, maxlength){
            if (text.length <= maxlength){
              return text
            }else{
              let dealed_text = text.slice(0,maxlength) + "..";
              return dealed_text  
            }
          },

          autoupdate: function(){
            setInterval(this.getlibrarydata, 60000);
            setInterval(this.fadeAction, 3000);
          }
          // showdataimg: function(dataindex){
          //   var that = this;
          //   if (that.librarydata[dataindex]){
          //     axios.get('/deploy/backend/tendaydata.php', {
          //       params: {
          //         device_id: that.librarydata[dataindex].device_id
          //       }
          //     })
          //     .then(function(response){
          //       console.log(response.data.Fin);
          //       Fin = response.data.Fin;
          //       dataimg.changeimg(Fin);
          //     })
          //   }
          // }
    },
    created(){
        this.getlibrarydata();
    },
    mounted(){
      this.autoupdate();
    }
})

// var dataimg = new Vue({
//   el: "#data_img",
//   data: {
//     Fin: [],
//     Fout: []
//   },
//   methods: { 
//    changeimg: function(Fin, Fout){
//       var dom = document.getElementById("data_img");
//       var myChart = echarts.init(dom);
//       option = null;
//       xlable = new Array();
//       nowdate = new Date();
//       for(let i= 0; i<Fin.length; i++){
//           x = nowdate.getMonth()+1 + "-" + nowdate.getDate();
//           xlable.push(x);
//           nowdate.setDate(nowdate.getDate()-1);
//           }

//       option = {
//           xAxis: {
//               type: 'category',
//               // data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
//               data: xlable
//           },
//           yAxis: {
//               type: 'value'
//           },
//           series: [{
//               // data: [820, 932, 901, 934, 1290, 1330, 1320],
//               data: Fin,
//               type: 'line',
//               smooth: true,
//               color: ["#2ece54"]
//           }]
//       };

//       function show(option){
//           if (option && typeof option === "object") {
//               myChart.setOption(option, true);
//           }
//       }
//       show(option);
//     }
//   },
//   mounted(){
    
//   }
// })


var dataimg = new Vue({
  el: "#data_img",
  data: {
    Fin: [],
    Fout: []
  },
  methods: { 
   drawimg: function(librarydata){
      console.log(librarydata);
      // librarydata object使用作为绘图数据
      var dom = document.getElementById("data_img");
      // 清空旧的图
      // dom.innerHTML = "";
      var myChart = echarts.init(dom, 'dark');
      var app = {};
      option = null;
      app.title = '坐标轴刻度与标签对齐';

      var Fin = new Array();
      var libraryname = new Array();
      for(let i in librarydata){
        // console.log(library);
        // 取出数据生成数据数组
        Fin.push(librarydata[i].Fin);
        libraryname.push(librarydata[i].deviceName);
      }
      // console.log(Fin);
      // console.log(libraryname);


      option = {
        color: ['#3398DB'],
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : libraryname,
                axisTick: {
                    alignWithLabel: true
                }
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'今日进馆人数',
                type:'bar',
                barWidth: '60%',
                data:Fin
            }
        ]
    };

      function show(option){
          if (option && typeof option === "object") {
              myChart.setOption(option, true);
          }
      }
      show(option);
    }
  },
  mounted(){
    
  }
})
