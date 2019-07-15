
function changeimg(Fin, Fout){
    var dom = document.getElementById("dataimg");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;
    Fin = ["45", "58"];
    xlable = new Array();
    nowdate = new Date();
    for(let i= 0; i<Fin.length; i++){
        x = nowdate.getMonth()+1 + "-" + nowdate.getDate();
        xlable.push(x);
        nowdate.setDate(nowdate.getDate()-1);
        }

    option = {
        xAxis: {
            type: 'category',
            // data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
            data: xlable
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            // data: [820, 932, 901, 934, 1290, 1330, 1320],
            data: Fin,
            type: 'line',
            smooth: true,
            color: ["#2ece54"]
        }]
    };

    function show(option){
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    }
    show(option);
    console.log(222);
}

// changeimg();
// changeimg();
// console.log(1111);
// var dom = document.getElementById("data_img");
// console.log(dom);
