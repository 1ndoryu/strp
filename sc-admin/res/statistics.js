var labels;

var datasets;

var chart;

const colors = {

    borrados: [

        "rgba(218, 32, 63, 0.6)",

        'rgb(218, 32, 63)'

    ],

    nuevos: [

        "rgba(32, 175, 218, 0.6)",

        "rgb(32, 175, 218)"

    ],

    renovados: [

        "rgba(32, 218, 48, 0.6)",

        "rgb(32, 218, 48)"

    ],
    renovados_premium: [

        "rgba(32, 218, 48, 0.6)",

        "rgb(32, 218, 48)"

    ],

    destacado: [

        "rgba(150, 32, 218, 0.6)",

        "rgb(150, 32, 218)"

    ],

    tops:[

        "rgba(32, 175, 218, 0.6)",

        "rgb(32, 175, 218)"

    ],

    autorenueva:[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ],

    diario:[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ],
    diario:[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ],

    autodiario:[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ],

    pedidos:[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ],

    "total-borrados": [

        "rgba(218, 32, 63, 0.6)",

        'rgb(218, 32, 63)'

    ],

    "total-anuncios": [

        "rgba(32, 175, 218, 0.6)",

        "rgb(32, 175, 218)"

    ],

    "total-usuarios": [

        "rgba(32, 218, 48, 0.6)",

        "rgb(32, 218, 48)"

    ],

    "total-listado": [

        "rgba(150, 32, 218, 0.6)",

        "rgb(150, 32, 218)"

    ],

    "total-tops":[

        "rgba(32, 175, 218, 0.6)",

        "rgb(32, 175, 218)"

    ],

    "total-banners":[

        "rgba(32, 218, 88, 0.6)",

        "rgb(32, 218, 88)"

    ]



  };

const label_list = {

    nuevos : "Anuncios publicados",

    borrados: "Anuncios eliminados",

    renovados: "Anuncios renovados",

    renovados_premium: "Anuncios renovados premium",

    visitas: "Visitas",

    listado: "Anuncios del listado",

    tops: "Anuncios tops",

    banners: "Banners",

    diario: "Diarios",

    autodiario: "Autodiarios",

    destacado: "Destacados",

    autorenueva: "Autorenueva",

    pedidos: "Pedidos",

    "total-borrados": "Total de anuncios borrados",

    "total-anuncios": "Total de anuncios",

    "total-listado": "Total de anuncios en el listado",

    "total-tops": "Total de anuncios top",

    "total-banners": "Total de banners",

    "total-usuarios": "Total usuarios"

}



$(document).ready(function () {

    const ctx = $('#estadisticas');

    labels = [];

    datasets = [

    ];



     chart = new Chart(ctx, {

        type: 'bar',

        data: {

            labels: labels,

            datasets: datasets

        },

        options: {

            scales: {

                y: {

                    beginAtZero: true

                },

                x:{

					grid:{

						display: false

					}

				}

            },

            plugins:{

				legend:{

					labels:{

						color: "#333",

						boxWidth: 20,

						font:{

							size: '18px'

						}

					},

					

				},

				tooltip: {

				

					events: ['click']

				}

			}

        }

    });



    $('#data_key, #mes').change(function(){

        update_data();

    });

    update_data();


});





function getDates(startDate, stopDate) {

    var dateArray = new Array();

    var currentDate = startDate;

    while (currentDate <= stopDate) {

        dateArray.push(new Date (currentDate));

        currentDate.setDate(currentDate.getDate() + 1);

    }

    return dateArray;

}



function getLast30Days()
{

    const start = new Date();

    start.setDate(start.getDate() - 30);

    const end = new Date();



    return getDates(start, end).map(function(val){

        return (val.toLocaleString('es', {month: 'short'}).length > 3 ? val.toLocaleString('es', {month: 'short'}).slice(0, -1) : val.toLocaleString('es', {month: 'short'})) + "-" +

        (val.getDate() >= 10 ? val.getDate() : "0"+val.getDate());

    });

}



function generate_dataset(keyword)
{   

    let dataset = {

        label: label_list[keyword],

        backgroundColor: colors[keyword][0],

        borderColor: colors[keyword][1],

        borderWidth: 1

    }; 

    return dataset;

}

function update_data()
{

    const _data = {}

    _data.key = $('#data_key').val();

    _data.mes = $('#mes').val();


    $.get(site_url + 'sc-admin/inc/ajax/stats.ajax.php', _data,

    function(res){

        data = res;
        update_chart();

    }, 'json');

}


function update_chart()

{
    labels = [];
    datasets = [];
    
    for (const key in data) 
    {
        let dataset = generate_dataset(key);

        array = data[key];

        if(labels.length == 0)
        {
            labels = Object.keys(array);
        }

        dataset.data = Object.values(array);

        datasets.push(dataset);
    }

    _reaload();

}



function _reaload()

{

    chart.data.labels = labels;

    chart.data.datasets = datasets;

    chart.update();

}



