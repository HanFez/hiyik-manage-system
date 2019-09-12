<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/2/18
 * Time: 10:57
 */
?>
    <script type="text/javascript" src="js/paper-full.min.js"></script>
    <script type="text/paperscript" canvas="myCanvas">
	var path;
    var types = ['point', 'handleIn', 'handleOut'];
    function findHandle(point) {
        for (var i = 0, l = path.segments.length; i < l; i++) {
            for (var j = 0; j < 3; j++) {
                var type = types[j];
                var segment = path.segments[i];
                var segmentPoint = type == 'point'
                        ? segment.point
                        : segment.point + segment[type];
                var distance = (point - segmentPoint).length;
                if (distance < 3) {
                    return {
                        type: type,
                        segment: segment
                    };
                }
            }
        }
        return null;
    }

    var currentSegment, mode, type;
    function onMouseDown(event) {
        if (currentSegment)
            currentSegment.selected = false;
        mode = type = currentSegment = null;

        if (!path) {
            path = new Path();
            path.strokeColor = 'black';
        }

        var result = findHandle(event.point);
        if (result) {
            currentSegment = result.segment;
            type = result.type;
            if (path.segments.length > 1 && result.type == 'point'
                    && result.segment.index == 0) {
                mode = 'close';
                path.closed = true;
                path.selected = true;
                //path = null;
                //returnData(mode);
            }
        }
        if (mode != 'close') {
            mode = currentSegment ? 'move' : 'add';
            if (!currentSegment)
                currentSegment = path.add(event.point);
            currentSegment.selected = true;
        }
	}

    function onMouseDrag(event) {
        if (mode == 'move' && type == 'point') {
            currentSegment.point = event.point;
            onMouseMove(event);
        } else if (mode != 'close') {
            var delta = event.delta.clone();
            if (type == 'handleOut' || mode == 'add')
                delta = -delta;
            currentSegment.handleIn += delta;
            currentSegment.handleOut -= delta;
        }
    }

    //显示鼠标当前坐标
    var text = new PointText(new Point(10,10));
    text.fillColor = 'red';
    function onMouseMove(event) {
	    text.content = '当前坐标位置: ' + event.point.toString();
	}
    //circle
    $('#circle').on('click',function(){
        path = new Path.Circle(new Point(400, 200), 150);
        path.strokeColor = 'black';
        path.selected = true;
    });
    //submit bezier
    $('#tijiao').on('click',function(){
        var clonePath = path.clone();
        clonePath.bounds.topLeft = new Point(0,0);
        //clonePath.visible = false;
        var arr = clonePath.curves;
        var point = new Array();
        for(var i=0,l=arr.length;i< l;i++){
            point.push(arr[i].values);
        }
        var points = new Array();
        for(var j=0,len=point.length;j<len;j++){
            var point1 = {};
            var point2 = {};
            var handle1 = {};
            var handle2 = {};
            point1 = {'x':point[j][0],'y':point[j][1]};
            handle1 = {'x':point[j][2],'y':point[j][3]};
            handle2 = {'x':point[j][4],'y':point[j][5]};
            point2 = {'x':point[j][6],'y':point[j][7]};
            points.push(point1,handle1,handle2,point2);
        }
        var viewport = {};
        viewport = {"x":clonePath.bounds.x,
                    "y":clonePath.bounds.y,
                    "width":clonePath.bounds.width,
                    "height":clonePath.bounds.height};
        clonePath.strokeWidth = 0;
        var g = new Group([clonePath]);
        var str = g.exportSVG({asString:true});
        //var xml = new XMLSerializer();
        //str = xml.serializeToString(str);
        params ={};
        params.data = {};
        params.data.points = points;
        params.data.viewport = viewport;
        params.data.str = str;
        ajaxData('post', 'new_pro/draw/section', function (result) {
            if(result.statusCode == 0) {
                bezier = result.data;
                messageAlert({
                    type: 'success',
                    message: result.message,
                    sticky:false
                })
            }
        }, [],params);
    });
</script>
    <style>
        canvas[resize]{
            width: 900px;
            height: 500px;
            /*box-sizing: border-box;
            border:1px solid;*/
        }
    </style>
<input type="button" id="circle" value="画圆"><br>
<canvas id="myCanvas" resize></canvas>
<input type="button" id="tijiao" value="提交">
