<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');
$passwd="admin";//修改密码
$logfilename = "./log.txt";
if(@$_SESSION['user']!=1){
	login();
	if(isset($_POST['pass'])){
		$pass=$_POST['pass'];
		login_check($pass);
	}
	die();
}

/**主功能部分**/

$ip = [];
$maxnum=10;//每页最大数量
$page=intval(@$_GET['p']);
empty($page)?$page=1:$page=intval(@$_GET['p']);
$file = fopen($logfilename, "r") or exit("Unable to open file!");
$filter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)|<.*(iframe|frame|style|embed|object|frameset|meta|xml|a|img)|hacker|eval\(.*\)|phpinfo\(\)|assert\(.*\)|\`|\~|\^|<\?php|[oc]:\d+:|pcntl_alarm|pcntl_fork|pcntl_waitpid|pcntl_wait|pcntl_wifexited|pcntl_wifstopped|pcntl_wifsignaled|pcntl_wifcontinued|pcntl_wexitstatus|pcntl_wtermsig|pcntl_wstopsig|pcntl_signal|pcntl_signal_get_handler|pcntl_signal_dispatch|pcntl_get_last_error|pcntl_strerror|pcntl_sigprocmask|pcntl_sigwaitinfo|pcntl_sigtimedwait|pcntl_exec|pcntl_getpriority|pcntl_setpriority|pcntl_async_signals|system\(.*\)|exec\(.*\)|shell_exec\(.*\)|popen\(.*\)|proc_open\(.*\)|passthru\(.*\)|symlink\(.*\)|link\(.*\)|syslog\(.*\)|imap_open\(.*\)|flag|cat\s|etc\spasswd|IFS|display_errors|catch|ini_set|set_time_limit(0)";

function Check_Flux($Value,$ArrFiltReq){
			foreach ($Value as $key => $value) {
				if(!is_array($value)){
					if (preg_match("/".$ArrFiltReq."/is",$value,$m)==1){
						return 1;
					}
				}else{
					if (preg_match("/".$ArrFiltReq."/is",implode($value))==1){
						return 1;
					}
				}
				
			}
			return 0;
		}
function login(){
	echo "<center><h3>日志审计系统登陆</h3></center>";
	echo "<center><form action=\"\" method=\"POST\">
	<input type=\"password\" name=\"pass\">
	<input type=\"submit\" name=\"submit\" value=\"Go\">
	</form></center>";
}
function jsonIndentFormat($jsonStr)
    {
        $result = '';
        $indentCount = 0;
        $strLen = strlen($jsonStr);
        $indentStr = '    ';
        $newLine = "\r\n";
        $isInQuotes = false;
        $prevChar = '';
        for($i = 0; $i <= $strLen; $i++) {
            $char = substr($jsonStr, $i, 1);
             
            if($isInQuotes){
                $result .= $char;
                if(($char=='"' && $prevChar!='\\')){
                    $isInQuotes = false;
                }
            }
            else{
                if(($char=='"' && $prevChar!='\\')){
                    $isInQuotes = true;
                    if ($prevChar != ':'){
                        $result .= $newLine;
                        for($j = 0; $j < $indentCount; $j++) {
                            $result .= $indentStr;
                        }
                    }
                    $result .= $char;
                }
                elseif(($char=='{' || $char=='[')){
                    if ($prevChar != ':'){
                        $result .= $newLine;
                        for($j = 0; $j < $indentCount; $j++) {
                            $result .= $indentStr;
                        }
                    }
                    $result .= $char;
                    $indentCount = $indentCount + 1;
                }
                elseif(($char=='}' || $char==']')){
                    $indentCount = $indentCount - 1;
                    $result .= $newLine;
                    for($j = 0; $j < $indentCount; $j++) {
                        $result .= $indentStr;
                    }
                    $result .= $char;
                }
                else{
                    $result .= $char;
                }
            }
            $prevChar = $char;
        }
        return $result;
    }
function login_check($pass){
	
	$passwd=$GLOBALS['passwd'];
	if($pass!=$passwd){//此处修改密码
		die("Password error!");
	}else{
		$_SESSION['user']=1;
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<style type="text/css">
	  a{text-decoration:none}
ul.pagination {
    display: inline-block;
    padding: 0;
    margin: 0;
}

ul.pagination li {display: inline;}

ul.pagination li a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
}

ul.pagination li a.active {
    background-color: #4CAF50;
    color: white;
}

ul.pagination li a:hover:not(.active) {background-color: #ddd;}

</style>
<title>日志</title>
<body >
	<center><h2>日志审计系统</h2></center>
	<table class="table table-border table-bordered table-bg" style="margin-top: 10px">
		<thead>
			<tr class="text-c">
				<th width="40">ID</th>
				<th >访问IP</th>
				<th width="150">数据</th>
				<th width="150">时间</th>
				<th width="130">类型判断</th>
			</tr>
		</thead>
				<tbody>
<?php
		$i=0;
		while(!feof($file))
		{
			$information = fgets($file);
			if(empty($information)||$page*$maxnum==$i){
				break;
			}elseif(empty(json_decode($information,true)['Path'])){
				continue;
			}elseif($i<($page-1)*$maxnum&&$page!=1){
				$i++;
				continue;
			}else{
				$i++;
			}
			if(Check_Flux(json_decode($information,true),$filter)){
			
				$style="danger";
			}elseif(isset(json_decode($information,true)['File'])){
				$style="upload";
			}else{
				$style="active";
			}
			$information = json_decode($information,true);
					?>
			<tr class="text-c <?php  if($style=="active"&&$i%2==0){
										echo "success";
										}elseif($style=="active"&&$i%2!=0){
										echo "active";
										}else{
											echo 'danger';
										}

			;?>">
				<td><?php echo $i;?></td>
				<td><?php echo $information['Userip'];?></td>
				<td ><pre id="json" style="width: 500px;height: 200px;font-size: 10px"><?php 
							echo  jsonIndentFormat(json_encode($information));
				?></pre></td>
				<td><?php echo $information['Time'];?></td>
				<td><?php
				if($style=="danger")
					echo"疑似攻击";
				elseif($style=="upload")
					echo "文件上传";
				else
					echo "正常";
				?></td>
			</tr>

	<?php	
}
	fclose($file);	?>
		</tbody>
	</table>
	<div style="margin-top: 10px" class="text-c ">
	<ul class="pagination">
	  		<li><a href="?p=<?php if($page>1)echo $page-1;?>">上一页</a></li>                     、
                <li><a href="?p=<?php echo $page+1;?>">下一页</a></li>	
   </ul>
	</div>

	<div class="text-c ">
	<!--<p target="_blank">共页<br>共1条数据</p>-->
</div>



<style type="text/css">
	    /*默认table*/
    table{width:100%;empty-cells:show;background-color:transparent;border-collapse:collapse;border-spacing:0}
    table th{text-align:left; font-weight:400}
    /*带水平线*/
    .table th{font-weight:bold}
    .table th,.table td{padding:8px;line-height:20px}
    .table td{text-align:left}
    .table tbody tr.success > td{background-color:#dff0d8}
    .table tbody tr.error > td{background-color:#f2dede}
    .table tbody tr.warning > td{background-color:#fcf8e3}
    .table tbody tr.info > td{background-color:#d9edf7}
    .table tbody + tbody{border-top:2px solid #ddd}
    .table .table{background-color:#fff}
     
    /*带横向分割线*/
    .table-border{border-top:1px solid #ddd}
    .table-border th,.table-border td{border-bottom:1px solid #ddd}
     
    /*th带背景*/
    .table-bg thead th{background-color:#F5FAFE}
    /*带外边框*/
    .table-bordered{border:1px solid #ddd;border-collapse:separate;*border-collapse:collapse;border-left:0}
    .table-bordered th,.table-bordered td{border-left:1px solid #ddd}
    .table-border.table-bordered{border-bottom:0}
     
    /*奇数行背景设为浅灰色*/
    .table-striped tbody > tr:nth-child(odd) > td,.table-striped tbody > tr:nth-child(odd) > th{background-color:#f9f9f9}
    /*竖直方向padding缩减一半*/
    .table-condensed th,.table-condensed td{padding:4px 5px}
    /*鼠标悬停样式*/
    .table-hover tbody tr:hover td,.table-hover tbody tr:hover th{background-color: #f5f5f5}
    /*定义颜色*/
    /*悬停在行*/
    .table tbody tr.active,.table tbody tr.active>td,.table tbody tr.active>th,.table tbody tr .active{background-color:#F5F5F5!important}
    /*成功或积极*/
    .table tbody tr.success,.table tbody tr.success>td,.table tbody tr.success>th,.table tbody tr .success{background-color:#DFF0D8!important}
     
    /*警告或出错*/
    .table tbody tr.warning,.table tbody tr.warning>td,.table tbody tr.warning>th,.table tbody tr .warning{background-color:#FCF8E3!important}
    /*危险*/
    .table tbody tr.danger,.table tbody tr.danger>td,.table tbody tr.danger>th,.table tbody tr .danger{background-color:#F2DEDE!important}
     
    /*表格文字对齐方式，默认是居左对齐*/
    .table .text-c th,.table .text-c td{text-align:center}/*整行居中*/
    .table .text-r th,.table .text-r td{text-align:right}/*整行居右*/
    .table th.text-l,.table td.text-l{text-align:left!important}/*单独列居左*/
    .table th.text-c,.table td.text-c{text-align:center!important}/*单独列居中*/
    .table th.text-r,.table td.text-r{text-align:right!important}/*单独列居右*/
        .text-l{text-align:left}/*水平居左*/
    .text-r{text-align:right}/*水平居中*/
    .text-c{text-align:center}/*水平居右*/
    .va *{vertical-align:sub!important;*vertical-align:middle!important;_vertical-align:middle!important}
    .va-t{ vertical-align:top!important}/*上下居顶*/
    .va-m{ vertical-align:middle!important}/*上下居中*/
    .va-b{ vertical-align:bottom!important}/*上下居底*/
     pre {
        outline: 1px solid #ccc; padding: 5px; margin: 5px; 
        text-align: left;
        white-space:pre-wrap;
        word-wrap:break-word;
        overflow-y:hidden;
		overflow-y:scroll;
    }
    .string { color: green; }
    .number { color: darkorange; }
    .boolean { color: blue; }
    .null { color: magenta; }
    .key { color: red; }
</style>
<script type="text/javascript">
// 方法实现
function syntaxHighlight(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
var preobject = document.getElementsByTagName("pre") ;
for(i=0;i<preobject.length;i++){
	preobject[i].innerHTML = (syntaxHighlight(preobject[i].innerText));
}
</script>
</body></html>