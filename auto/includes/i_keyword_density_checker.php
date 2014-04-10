<?php
echo "<br><br><hr>";
$string = $_POST['string'];
$string = urldecode($string);
$string = strip_tags($string);
$stop_words_1 = strtolower("at,th,co,but,not,pm,am,car,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,_,-,','s,yet,went,told,launching,'t,off,has,am,page,released,world,can,get,longer,stock,met,seen,content,can't,plus,got,go,no,review,added,new,we,all,check,our,be,hire,night,file,incredible,list,mostly,finally,detail,|,of,add,minus,subtract,table,about,above,acid,across,actually,after,again,against,almost,already,also,alter,although,always,among,angry,another,anyway,appropriate,around,automatic,available,awake,aware,away,back,basic,beautiful,because,been,before,being,bent,better,between,bitter,black,blue,boiling,both,bright,broken,brown,came,cause,central,certain,certainly,cheap,chemical,chief,clean,clear,clearly,close,cold,come,common,complete,complex,concerned,conscious,could,cruel,current,dark,dead,dear,deep,delicate,dependent,different,difficult,dirty,down,each,early,east,easy,economic,either,elastic,electric,else,enough,equal,especially,even,ever,every,exactly,feeble,female,fertile,final,finalty,financial,fine,first,fixed,flat,following,foolish,foreign,form,former,forward,free,frequent,from,full,further,future,general,generality,give,good,great,green,grey/gray,half,hanging,happy,hard,have,healthy,heavy,help,here,high,himself,hollow,home,however,human,important,indeed,individual,industrial,instead,international,into,just,keep,kind,labor,large,last,late,later,least,left,legal,less,like,likely,line,little,living,local,long,loose,loud,main,major,make,male,many,married,material,maybe,mean,medical,might,military,mixed,modern,more,most,much,must,name,narrow,national,natural,near,nearly,necessary,never,next,nice,normal,north,obviously,often,okay,once,only,open,opposite,original,other,over,parallel,particular,particularly,past,perhaps,personal,physical,please,political,poor,popular,possible,present,previous,prime,private,probable,probably,professional,public,quick,quickly,quiet,quite,rather,ready,real,really,recent,recently,regular,responsible,right,rough,round,royal,safe,said,same,second,secret,seem,send,separate,serious,several,shall,sharp,short,should,shut,significant,similar,simple,simply,since,single,slow,small,smooth,social,soft,solid,some,sometimes,soon,sorry,south,special,specific,sticky,stiff,still,straight,strange,strong,successful,such,sudden,suddenly,sure,sweet,take,tall,than,that,their,them,then,there,therefore,these,they,thick,thin,think,this,those,though,through,thus,tight,till,tired,today,together,tomorrow,total,turn,under,unless,until,upon,used,useful,usually,various,very,violent,waiting,warm,well,were,west,what,whatever,when,where,whether,which,while,white,whole,whose,wide,will,wise,with,within,without,would,wrong,yeah,yellow,yesterday,young,your,anyone,builds,tried,after,before,when,while,since,until,although,though,even,while,if,unless,only,case,that,this,because,since,now,as,in,on,around,to,I,he,she,it,they,them,both,either,and,top,most,best,&,inside,for,their,from,one,two,three,four,five,six,seven,eight,nine,ten,1,2,3,4,5,6,7,8,9,0,user,inc,is,isn't,are,aren't,do,don't,does,anyone,really,too,over,under,into,the,a,an,my,mine,against,inbetween,me,~,*,was,you,with,your,will,win,by");
$stop_words_1 = explode(",", $stop_words_1);
$stop_words_2 = strtolower("to be,of the,of it,a better,do a,to do,at the,after the,with the,in the,into a,last year,he told,she told,it is,something is,on the,on a,");
$stop_words_2 = explode(",", $stop_words_2);
$stop_words_3 = strtolower("to do a,do a better");
$stop_words_3 = explode(",", $stop_words_3);


// str_word_count($str,1) - returns an array containing all the words found inside the string
$string = strtolower($string);
$words = str_word_count($string,2);
$words = array_values($words);
$count= count($words);

//print_r($words);exit;
//generate phrases
foreach ($words as $key=>$val)
{
	$plus = $key+1;
	$plusplus = $plus+1;
	$phrase_1[] = $val;
	$phrase_2[] = "{$words[$key]} {$words[$plus]}";
	$phrase_3[] = "{$words[$key]} {$words[$plus]} {$words[$plusplus]}";
	//echo "{$words[$key]} {$words[$plus]} {$words[$plusplus]} : $key, $plus, $plusplus<br>";
}


//////////////PHRASE 3 GO/////////////////////

echo"<h3>3 Word Phrase Breakdown</h3>";
echo "<table>";
echo "<tr><td width=300><i>keyword</i></td><td width=300><i># occurance</i></td><td width=300><i>density</i></td></tr>";

$phrase_3_total = count($phrase_3);
$phrase_3 = array_unique($phrase_3);

foreach ($phrase_3 as $key=>$val)
{
	$this_count = substr_count($string, $val);
	$this_density = $this_count / $phrase_3_total;
	$this_density = $this_density * 100;
	$this_density = number_format($this_density, 2, '.', '');
	
	if ($this_count>1&&!in_array($val, $stop_words_1))
	{
		//echo $val;
		//echo "<hr>";
		$phrase_3_array[] = array('keyword'=> $val , 'occurance'=>$this_count, 'density'=>$this_density."%");
		$phrase_3_keyword[] = $val; 
		$phrase_3_occurance[] = $this_count; 
		$phrase_3_density[] = $this_desnsity."%"; 
	}
}

array_multisort($phrase_3_density, SORT_DESC, $phrase_3_occurance, SORT_DESC, $phrase_3_array);
//$phrase_3_array = array_unique($phrase_3_array);

foreach ( $phrase_3_array as $key=>$val)
{
	echo "<tr><td>{$phrase_3_array[$key]['keyword']}</td><td>{$phrase_3_array[$key]['occurance']}</td><td>{$phrase_3_array[$key]['density']}</td></tr>";
}
echo "</table>";
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////PHRASE 2 GO/////////////////////
echo"<h3>2 Word Phrase Breakdown</h3>";
echo "<table>";
echo "<tr><td width=300><i>keyword</i></td><td width=300><i># occurance</i></td><td width=300><i>density</i></td></tr>";

$phrase_2_total = count($phrase_2);
$phrase_2 = array_unique($phrase_2);

foreach ($phrase_2 as $key=>$val)
{
	$this_count = substr_count($string, $val);
	$this_density = $this_count / $phrase_2_total;
	$this_density = $this_density * 100;
	$this_density = number_format($this_density, 2, '.', '');
	
	if ($this_count>1&&!in_array($val, $stop_words_2))
	{
		//echo $val;
		//echo "<hr>";
		$phrase_2_array[] = array('keyword'=> $val , 'occurance'=>$this_count, 'density'=>$this_density."%");
		$phrase_2_keyword[] = $val; 
		$phrase_2_occurance[] = $this_count; 
		$phrase_2_density[] = $this_desnsity."%"; 
	}
}

array_multisort($phrase_2_density, SORT_DESC, $phrase_2_occurance, SORT_DESC, $phrase_2_array);

foreach ( $phrase_2_array as $key=>$val)
{
	echo "<tr><td>{$phrase_2_array[$key]['keyword']}</td><td>{$phrase_2_array[$key]['occurance']}</td><td>{$phrase_2_array[$key]['density']}</td></tr>";
}
echo "</table>";
// array_count_values() returns an array using the values of the input array as keys and their frequency in input as values.
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////PHRASE 1 GO/////////////////////
echo"<h3>Single Word Breakdown</h3>";
echo "<table>";
echo "<tr><td width=300><i>keyword</i></td><td width=300><i># occurance</i></td><td width=300><i>density</i></td></tr>";

$phrase_1_total = count($phrase_1);
$phrase_1 = array_unique($phrase_1);

foreach ($phrase_1 as $key=>$val)
{
	$this_count = substr_count($string, $val);
	$this_density = $this_count / $phrase_1_total;
	$this_density = $this_density * 100;
	$this_density = number_format($this_density, 2, '.', '');
	
	if ($this_count>1&&!in_array($val, $stop_words_1))
	{
		//echo $val;
		//echo "<hr>";
		$phrase_1_array[] = array('keyword'=> $val , 'occurance'=>$this_count, 'density'=>$this_density."%");
		$phrase_1_keyword[] = $val; 
		$phrase_1_occurance[] = $this_count; 
		$phrase_1_density[] = $this_desnsity."%"; 
	}
}

array_multisort($phrase_1_density, SORT_DESC, $phrase_1_occurance, SORT_DESC, $phrase_1_array);

foreach ( $phrase_1_array as $key=>$val)
{
	echo "<tr><td>{$phrase_1_array[$key]['keyword']}</td><td>{$phrase_1_array[$key]['occurance']}</td><td>{$phrase_1_array[$key]['density']}</td></tr>";
}
echo "</table>";
// array_count_values() returns an array using the values of the input array as keys and their frequency in input as values.
?>
