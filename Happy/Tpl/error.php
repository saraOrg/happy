<html>
    <head>
        <title>出错啦。。。</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    </head>
    <style type="text/css">
        #debug-info {
            padding: 0;
            width: 888px;
            margin: 20px;
            background: #fff;
        }
        .error {
            color: red;
            font-weight: bold;
            font-size: 1.2em;
        }
        fieldset {
            border: 1px solid #ccc;
            padding: 10px;
            color: #323232;
            font-size: 16px;
        }
        legend {
            font-weight: bold;
            color: #000;
            font-size: 1.5em;
        }
        ol.linenums {
            margin-top: -5px;
            margin-bottom: 10px;
            color: #2BA5D8;
            font-weight: bold;
        }
        ol li {
            padding-top:5px;
        }
        ol span {
            font-weight: normal;
            color: #323232;
        }
    </style>
    <body>
        <div id="debug-info">
            <h2><?php echo $e['message'];?></h2>
            <?php if (isset($e['info'])) { ?>
                <fieldset>
                    <legend>TRACE</legend>
                    <?php echo $e['info']; ?>
                </fieldset>
            <?php } ?>
        </div>
    </body>
</html>