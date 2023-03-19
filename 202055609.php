<?php
    $mysqli = new mysqli("localhost", "root", "303360", "202055609", "3306");

    $title = $_POST['linkTitle'];
    $src = $_POST['linkSrc'];
    $add = $_POST['addBtn'];
    $del = $_POST['delBtn'];
    $clear = $_POST['ClearBtn'];

    if(!empty($add)) {
        $mysqli->query("insert ignore into links (title, src) values ('".$title."', '".$src."')");
        $bool = $mysqli->affected_rows;
    }

    else if(!empty($del)) {
        $mysqli->query("delete from links where links.title = '".$title."'");
    }

    else if(!empty($clear)) {
        $mysqli->query("TRUNCATE TABLE links");
    }

    $links = $mysqli->query("select * from links");
    $len = $links->num_rows;
    $arr = array();
    for($i=0; $i < $len; $i++) {
        $data = $links->fetch_array(); 
        $arr[$i] = array("title" =>  $data['title'] , "src" => $data['src']);
    }             
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Example</title>
    <style type="text/css">
        #linkContainer {
            display: flex;
            flex-wrap: wrap;
            width: 310px;
        }
    
        #linkContainer a  {
            display: block;
            list-style-type: none;
            text-align: center;
            font-size: 3em;
            border: 1px solid;
            width: 150px;
            float: left;
            margin: 1px;
        }        
        a   { 
            text-decoration: none;
        }
        .pagination {
            visibility: hidden;
            display: inline-block;
        }
        .pagination div {
            float: left;
            padding: 8px 16px;
        }
        .pagination div.active {
            background-color: #4CAF50;
            color: white;
        }
        .pagination div:hover:not(.active) {background-color: #ddd;}
        .maindiv {
            width: 320px;
            float: left;
        }
        iframe {
            width: 500px;
            height: 500px;
            float: left;
        }
    </style>
    <script type="text/javascript">
        var linkArr = new Array();
        var curPage = 0;

        function addLink()
        {
            if (linkArr.findIndex(checkTitle) >= 0)
            {
                window.alert("이미 있는 즐겨찾기 제목입니다. 다른 제목을 사용해주세요.");
                return;
            }
            linkArr.push({title: document.forms[0].linkTitle.value, src: document.forms[0].linkSrc.value});
            linkArr.sort(compareTitle);
            
            makePages();
            showLinks();
        }
        function checkTitle(link)
        {
            return link.title == document.forms[0].linkTitle.value;
        }
        function compareTitle(link1, link2)
        {
            if (link1.title > link2.title)
                return 1;
            else if (link1.title < link2.title)
                return -1;
            else
                return 0;
        }       
        function deleteLink()
        {
            var idx = linkArr.findIndex(checkTitle);
            if (idx < 0)
            {
                window.alert("일치하는 제목을 가진 즐겨찾기가 없습니다.");
                return;
            }
            linkArr.splice(idx, 1);
            makePages();
            showLinks();
        }
        function clearAll()
        {
            linkArr = Array();
            document.getElementById("linkContainer").innerHTML = "";
        }
        function showLinks()
        {
            var linkContainer = document.getElementById("linkContainer");
            linkContainer.innerHTML = "";

            var startIdx = curPage * 10;
            var endIdx = startIdx + 9;
            for(var idx = startIdx; idx <= endIdx && idx < linkArr.length; idx++)
            {
                var link = makeLink(linkArr[idx].title, linkArr[idx].src);
                linkContainer.innerHTML += link;
            }
        }
        function makeLink(title, src)
        {
            return "<a href='" + src + "' target=\"linkFrame\">" + title + "</a>";
        }
        function makePages()
        {
            if (linkArr.length > 10)
            {
                var pageNav = document.getElementById("pageNav");
                pageNav.style.visibility = "visible";
                pageNav.innerHTML = "";
                var nPage = Math.ceil(linkArr.length / 10);
                for(var i =0; i < nPage; i++)
                {
                    if (i == curPage)
                        pageNav.innerHTML += "<div class=\"active\" onclick=\"changePage(" + i +")\">" + (i + 1) + "</div>";
                    else
                        pageNav.innerHTML += "<div onclick=\"changePage(" + i +")\">" + (i + 1) + "</div>";

                }
            }
        }
        function changePage(pageNum)
        {
            curPage = pageNum;
            makePages();
            showLinks();
        }
        function start()
        {
            if(<?php echo $len ?> > 0) {
                linkArr = 
                <?php
                    echo json_encode($arr); 
                ?>;
                makePages();
                showLinks();
            }
        }
    </script>
    </head>
<body onload="start()">
    <div class="maindiv">
        <form name="form1" method="POST" action="202055609.php">
            <label>즐겨찾기 제목: <input type="text" name="linkTitle" id="linkTitle"></label><br>
            <label>즐겨찾기 링크: <input type="text" name="linkSrc" id="linkSrc"></label><br>
            <input type="submit" name="addBtn" id="addBtn" value="즐겨찾기 추가" onclick="addLink()">
            <input type="submit" name="delBtn" id="delBtn" value="즐겨찾기 삭제" onclick="deleteLink()">
            <input type="submit" name="ClearBtn" id="clearBtn" value="모두 삭제" onclick="clearAll()">
        </form>
        <br>
        <div id="linkContainer">
        </div>
        <nav class="pagination" id="pageNav">
        </nav>
    </div>
    <iframe title="iframe for link" name="linkFrame"></iframe>
</body>
</html>
<?php
if(!empty($del) || $bool === 0) {
    echo ("<script>document.forms[0].linkTitle.value = '".$title."';</script>");
    echo ("<script>document.forms[0].linkSrc.value = '".$src."';</script>");
}
?>