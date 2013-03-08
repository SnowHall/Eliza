<!doctype>
<link type="text/css" rel="stylesheet" href="/eliza/assets/js/rickshaw/rickshaw.min.css">
<link type="text/css" rel="stylesheet" href="/eliza/assets/js/rickshaw/src/css/graph.css">
<link type="text/css" rel="stylesheet" href="/eliza/assets/js/rickshaw/src/css/detail.css">
<link type="text/css" rel="stylesheet" href="/eliza/assets/js/rickshaw/src/css/legend.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
<script src="/eliza/assets/js/rickshaw/vendor/d3.min.js"></script>
<script src="/eliza/assets/js/rickshaw/vendor/d3.layout.min.js"></script>
<script src="/eliza/assets/js/rickshaw/rickshaw.min.js"></script>

<h3>Test failed status graph (last week)</h3>
<div class="chart_container">
    <div id="y_axis_day"></div>
    <div id="legend_day"></div>
    <div id="chart_day"></div>
    <div id="slider"></div>
    <!--<div id="legend_day"></div>-->
</div>

<h3>Test failed status graph (last month)</h3>
<div class="chart_container">
    <div id="y_axis_month"></div>
    <div id="chart_month"></div>
    <div id="legend_month"></div>
    <div id="slider"></div>
</div>

<script>
var palette = new Rickshaw.Color.Palette();

var graph = new Rickshaw.Graph( {
        element: document.querySelector("#chart_day"),
        width: 550,
        height: 200,
        series: [
                {
                    name: "Failed",
                    //data: [ { x: -1893456000, y: 25868573 }, { x: -1577923200, y: 29662053 }, { x: -1262304000, y: 34427091 }, { x: -946771200, y: 35976777 }, { x: -631152000, y: 39477986 }, { x: -315619200, y: 44677819 }, { x: 0, y: 49040703 }, { x: 315532800, y: 49135283 }, { x: 631152000, y: 50809229 }, { x: 946684800, y: 53594378 }, { x: 1262304000, y: 55317240 } ],
                    data: <?php echo $weekReport; ?>,
                    color: palette.color()
                },
        ]
} );

var hoverDetail = new Rickshaw.Graph.HoverDetail( {
	graph: graph
} );

var shelving = new Rickshaw.Graph.Behavior.Series.Toggle( {
	graph: graph,
	legend: legend
} );

var x_axis = new Rickshaw.Graph.Axis.Time( { graph: graph } );

var y_axis = new Rickshaw.Graph.Axis.Y( {
        graph: graph,
        orientation: 'left',
        tickFormat: Rickshaw.Fixtures.Number.formatPercents,
        tick: 10,
        tickSize: 10,
        pixelsPerTick: 20,
        element: document.getElementById('y_axis_day'),
        width: 60
} );

var legend = new Rickshaw.Graph.Legend( {
        element: document.querySelector('#legend_day'),
        graph: graph
} );

graph.render();

/* Month report */

var palette_month = new Rickshaw.Color.Palette();

var graph_month = new Rickshaw.Graph( {
        element: document.querySelector("#chart_month"),
        width: 550,
        height: 200,
        series: [
                {
                    name: "Failed",
                    //data: [ { x: -1893456000, y: 25868573 }, { x: -1577923200, y: 29662053 }, { x: -1262304000, y: 34427091 }, { x: -946771200, y: 35976777 }, { x: -631152000, y: 39477986 }, { x: -315619200, y: 44677819 }, { x: 0, y: 49040703 }, { x: 315532800, y: 49135283 }, { x: 631152000, y: 50809229 }, { x: 946684800, y: 53594378 }, { x: 1262304000, y: 55317240 } ],
                    data: <?php echo $monthReport; ?>,
                    color: palette_month.color()
                },
        ]
} );

var x_axis_month = new Rickshaw.Graph.Axis.Time( { graph: graph_month } );

var y_axis_month = new Rickshaw.Graph.Axis.Y( {
        graph: graph_month,
        orientation: 'left',
        tickFormat: Rickshaw.Fixtures.Number.formatPercents,
        tick: 10,
        tickSize: 10,
        pixelsPerTick: 20,
        element: document.getElementById('y_axis_month'),
        width: 60
} );
var hoverDetail = new Rickshaw.Graph.HoverDetail( {
	graph: graph_month
} );

var shelving = new Rickshaw.Graph.Behavior.Series.Toggle( {
	graph: graph_month,
	legend: legend
} );
var legend = new Rickshaw.Graph.Legend( {
        element: document.querySelector('#legend_month'),
        graph: graph_month
} );

graph_month.render();

</script>
