var style_cookie;var style_cookie_txt;var style_cookie_site;var kumod_set=false;var ispage;if(!Array.prototype.indexOf){Array.prototype.indexOf=function(B){var A=this.length;var C=Number(arguments[1])||0;C=(C<0)?Math.ceil(C):Math.floor(C);if(C<0){C+=A}for(;C<A;C++){if(C in this&&this[C]===B){return C}}return -1}}var Utf8={encode:function(B){B=B.replace(/\r\n/g,"\n");var A="";for(var D=0;D<B.length;D++){var C=B.charCodeAt(D);if(C<128){A+=String.fromCharCode(C)}else{if((C>127)&&(C<2048)){A+=String.fromCharCode((C>>6)|192);A+=String.fromCharCode((C&63)|128)}else{A+=String.fromCharCode((C>>12)|224);A+=String.fromCharCode(((C>>6)&63)|128);A+=String.fromCharCode((C&63)|128)}}}return A},decode:function(A){var B="";var C=0;var D=c1=c2=0;while(C<A.length){D=A.charCodeAt(C);if(D<128){B+=String.fromCharCode(D);C++}else{if((D>191)&&(D<224)){c2=A.charCodeAt(C+1);B+=String.fromCharCode(((D&31)<<6)|(c2&63));C+=2}else{c2=A.charCodeAt(C+1);c3=A.charCodeAt(C+2);B+=String.fromCharCode(((D&15)<<12)|((c2&63)<<6)|(c3&63));C+=3}}}return B}};function replaceAll(B,D,C){var A=B.indexOf(D);while(A>-1){B=B.replace(D,C);A=B.indexOf(D)}return B}function insert(D){var B=document.forms.postform.message;if(B){if(B.createTextRange&&B.caretPos){var C=B.caretPos;C.text=C.text.charAt(C.text.length-1)==" "?D+" ":D}else{if(B.setSelectionRange){var E=B.selectionStart;var A=B.selectionEnd;B.value=B.value.substr(0,E)+D+B.value.substr(A);B.setSelectionRange(E+D.length,E+D.length)}else{B.value+=D+" "}}B.focus()}}function quote(b,a){var v=eval("document."+a+".message");v.value+=">>"+b+"\n";v.focus()}function checkhighlight(){var A;if(A=/#i([0-9]+)/.exec(document.location.toString())){if(!document.forms.postform.message.value){insert(">>"+A[1])}}if(A=/#([0-9]+)/.exec(document.location.toString())){highlight(A[1])}}function highlight(D,F){if(F&&ispage){return }var B=document.getElementsByTagName("td");for(var C=0;C<B.length;C++){if(B[C].className=="highlight"){B[C].className="reply"}}var E=document.getElementById("reply"+D);if(E){E.className="highlight";var A=/^([^#]*)/.exec(document.location.toString());document.location=A[1]+"#"+D}}function get_password(A){var E=getCookie(A);if(E){return E}var D="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";var E="";for(var C=0;C<8;C++){var B=Math.floor(Math.random()*D.length);E+=D.substring(B,B+1)}set_cookie(A,E,365);return(E)}function togglePassword(){var C=(navigator.userAgent.indexOf("Safari")!=-1);var F=(navigator.userAgent.indexOf("Opera")!=-1);var B=(navigator.appName=="Netscape");var A=document.getElementById("passwordbox");var E;if((C)||(F)||(B)){E=A.innerHTML}else{E=A.text}E=E.toLowerCase();var D="<td></td><td></td>";if(E==D){var D="<td class=\"postblock\">Mod</td><td><input type=\"text\" name=\"modpassword\" size=\"28\" maxlength=\"75\">&nbsp;<acronym title=\"Distplay staff status (Mod/Admin)\">D</acronym>:&nbsp;<input type=\"checkbox\" name=\"displaystaffstatus\" checked>&nbsp;<acronym title=\"Lock\">L</acronym>:&nbsp;<input type=\"checkbox\" name=\"lockonpost\">&nbsp;&nbsp;<acronym title=\"Sticky\">S</acronym>:&nbsp;<input type=\"checkbox\" name=\"stickyonpost\">&nbsp;&nbsp;<acronym title=\"Raw HTML\">RH</acronym>:&nbsp;<input type=\"checkbox\" name=\"rawhtml\">&nbsp;&nbsp;<acronym title=\"Name\">N</acronym>:&nbsp;<input type=\"checkbox\" name=\"usestaffname\"></td>"}if((C)||(F)||(B)){A.innerHTML=D}else{A.text=D}return false}function getCookie(name){with(document.cookie){var regexp=new RegExp("(^|;\\s+)"+name+"=(.*?)(;|$)");var hit=regexp.exec(document.cookie);if(hit&&hit.length>2){return Utf8.decode(unescape(replaceAll(hit[2],"+","%20")))}else{return""}}}function set_cookie(C,D,E){if(E){var B=new Date();B.setTime(B.getTime()+(E*24*60*60*1000));var A="; expires="+B.toGMTString()}else{A=""}document.cookie=C+"="+D+A+"; path=/"}function set_stylesheet(H,B,D){if(B){set_cookie("kustyle_txt",H,365)}else{if(D){set_cookie("kustyle_site",H,365)}else{set_cookie("kustyle",H,365)}}var C=document.getElementsByTagName("link");var F=false;for(var E=0;E<C.length;E++){var A=C[E].getAttribute("rel");var G=C[E].getAttribute("title");if(A.indexOf("style")!=-1&&G){C[E].disabled=true;if(H==G){C[E].disabled=false;F=true}}}if(!F){set_preferred_stylesheet()}}function set_preferred_stylesheet(){var B=document.getElementsByTagName("link");for(var C=0;C<B.length;C++){var A=B[C].getAttribute("rel");var D=B[C].getAttribute("title");if(A.indexOf("style")!=-1&&D){B[C].disabled=(A.indexOf("alt")!=-1)}}}function get_active_stylesheet(){var B=document.getElementsByTagName("link");for(var C=0;C<B.length;C++){var A=B[C].getAttribute("rel");var D=B[C].getAttribute("title");if(A.indexOf("style")!=-1&&D&&!B[C].disabled){return D}}return null}function get_preferred_stylesheet(){var B=document.getElementsByTagName("link");for(var C=0;C<B.length;C++){var A=B[C].getAttribute("rel");var D=B[C].getAttribute("title");if(A.indexOf("style")!=-1&&A.indexOf("alt")==-1&&D){return D}}return null}function delandbanlinks(){if(!kumod_set){return }var B=document.getElementsByTagName("span");var E;var A;for(var C=0;C<B.length;C++){E=B[C];if(E.getAttribute("class")){if(E.getAttribute("class").substr(0,3)=="dnb"){A=E.getAttribute("class").split("|");var D="&#91;<a href=\""+ku_cgipath+"/manage_page.php?action=delposts&boarddir="+A[1]+"&del";if(A[3]=="y"){D+="thread"}else{D+="post"}D+="id="+A[2]+"\" title=\"Delete\" onclick=\"return confirm('Are you sure you want to delete this post/thread?');\">D</a>&nbsp;<a href=\""+ku_cgipath+"/manage_page.php?action=delposts&boarddir="+A[1]+"&del";if(A[3]=="y"){D+="thread"}else{D+="post"}D+="id="+A[2]+"&postid="+A[2]+"\" title=\"Delete &amp; Ban\" onclick=\"return confirm('Are you sure you want to delete and ban the poster of this post/thread?');\">&amp;</a>&nbsp;<a href=\""+ku_cgipath+"/manage_page.php?action=bans&banboard="+A[1]+"&banpost="+A[2]+"\" title=\"Ban\">B</a>&#93;";B[C].innerHTML=D}}}}function togglethread(A){if(hiddenthreads.toString().indexOf(A)!==-1){document.getElementById("unhidethread"+A).style.display="none";document.getElementById("thread"+A).style.display="block";hiddenthreads.splice(hiddenthreads.indexOf(A),1);set_cookie("hiddenthreads",hiddenthreads.join("!"),30)}else{document.getElementById("unhidethread"+A).style.display="block";document.getElementById("thread"+A).style.display="none";hiddenthreads.push(A);set_cookie("hiddenthreads",hiddenthreads.join("!"),30)}return false}function toggleblotter(C){var B=document.getElementsByTagName("li");var A=new Array();var D;for(i=0,iarr=0;i<B.length;i++){att=B[i].getAttribute("class");if(att=="blotterentry"){D=B[i];if(D.style.display=="none"){D.style.display="";if(C){set_cookie("ku_showblotter","1",365)}}else{D.style.display="none";if(C){set_cookie("ku_showblotter","0",365)}}}}}function expandthread(C,A){if(document.getElementById("replies"+C+A)){var B=document.getElementById("replies"+C+A);B.innerHTML="Expanding thread...<br><br>"+B.innerHTML;new Ajax.Request(ku_boardspath+"/expand.php?board="+A+"&threadid="+C,{method:"get",onSuccess:function(E){var D=E.responseText||"something went wrong (blank response)";B.innerHTML=D;delandbanlinks()},onFailure:function(){alert("Something went wrong...")}})}return false}function quickreply(A){if(A==0){document.getElementById("posttypeindicator").innerHTML="new thread"}else{document.getElementById("posttypeindicator").innerHTML="reply to "+A+" [<a href=\"#postbox\" onclick=\"javascript:quickreply('0');\" title=\"Cancel\">x</a>]"}document.postform.replythread.value=A}function getwatchedthreads(C,B){if(document.getElementById("watchedthreadlist")){var A=document.getElementById("watchedthreadlist");A.innerHTML="Loading watched threads...";new Ajax.Request(ku_boardspath+"/threadwatch.php?board="+B+"&threadid="+C,{method:"get",onSuccess:function(E){var D=E.responseText||"something went wrong (blank response)";A.innerHTML=D},onFailure:function(){alert("Something went wrong...")}})}}function addtowatchedthreads(B,A){if(document.getElementById("watchedthreadlist")){new Ajax.Request(ku_boardspath+"/threadwatch.php?do=addthread&board="+A+"&threadid="+B,{method:"get",onSuccess:function(D){var C=D.responseText||"something went wrong (blank response)";alert("Thread successfully added to your watch list.");getwatchedthreads("0",A)},onFailure:function(){alert("Something went wrong...")}})}}function removefromwatchedthreads(B,A){if(document.getElementById("watchedthreadlist")){new Ajax.Request(ku_boardspath+"/threadwatch.php?do=removethread&board="+A+"&threadid="+B,{method:"get",onSuccess:function(D){var C=D.responseText||"something went wrong (blank response)";getwatchedthreads("0",A)},onFailure:function(){alert("Something went wrong...")}})}}function hidewatchedthreads(){set_cookie("showwatchedthreads","0",30);if(document.getElementById("watchedthreads")){document.getElementById("watchedthreads").innerHTML="The Watched Threads box will be hidden the next time a page is loaded.  [<a href=\"#\" onclick=\"javascript:showwatchedthreads();return false\">undo</a>]"}}function showwatchedthreads(){set_cookie("showwatchedthreads","1",30);window.location.reload(true)}function checkcaptcha(A){if(document.getElementById(A)){if(document.getElementById(A).captcha){if(document.getElementById(A).captcha.value==""){alert("Please enter the captcha image text.");document.getElementById(A).captcha.focus();return false}}}return true}function expandimg(I,H,F,C,G,E,A){element=document.getElementById("thumb"+I);var D="<img src=\""+F+"\" alt=\""+I+"\" class=\"thumb\" height=\""+A+"\" width=\""+E+"\">";var B="<img class=thumb height="+A+" alt="+I+" src=\""+F+"\" width="+E+">";if(element.innerHTML.toLowerCase()!=D&&element.innerHTML.toLowerCase()!=B){element.innerHTML=D}else{element.innerHTML="<img src=\""+H+"\" alt=\""+I+"\" class=\"thumb\" height=\""+G+"\" width=\""+C+"\">"}}function postpreview(D,A,C,B){if(document.getElementById(D)){new Ajax.Request(ku_boardspath+"/expand.php?preview&board="+A+"&parentid="+C+"&message="+escape(B),{method:"get",onSuccess:function(F){var E=F.responseText||"something went wrong (blank response)";document.getElementById(D).innerHTML=E},onFailure:function(){alert("Something went wrong...")}})}}function set_inputs(id){if(document.getElementById(id)){with(document.getElementById(id)){if(!name.value){name.value=getCookie("name")}if(!em.value){em.value=getCookie("email")}if(!postpassword.value){postpassword.value=get_password("postpassword")}}}}function set_delpass(id){if(document.getElementById(id).postpassword){with(document.getElementById(id)){if(!postpassword.value){postpassword.value=get_password("postpassword")}}}}function keypress(F){if(F.altKey){var C=document.location.toString();if((C.indexOf("catalog.html")==-1&&C.indexOf("/res/")==-1)||(C.indexOf("catalog.html")==-1&&F.keyCode==80)){if(F.keyCode!=18&&F.keyCode!=16){if(C.indexOf(".html")==-1||C.indexOf("board.html")!=-1){var D=0;var G=C.substr(0,C.lastIndexOf("/")+1)}else{var D=C.substr((C.lastIndexOf("/")+1));D=(+D.substr(0,D.indexOf(".html")));var G=C.substr(0,C.lastIndexOf("/")+1)}if(D==0){var B=G}else{var B=G+D+".html"}if(F.keyCode==222||F.keyCode==221){if(match=/#s([0-9])/.exec(C)){var A=(+match[1])}else{var A=-1}if(F.keyCode==222){if(A==-1||A==9){var E=0}else{var E=A+1}}else{if(F.keyCode==221){if(A==-1||A==0){var E=9}else{var E=A-1}}}document.location.href=B+"#s"+E}else{if(F.keyCode==59||F.keyCode==219){if(F.keyCode==59){D=D+1}else{if(F.keyCode==219){if(D>=1){D=D-1}}}if(D==0){document.location.href=G}else{document.location.href=G+D+".html"}}else{if(F.keyCode==80){document.location.href=B+"#postbox"}}}}}}}window.onunload=function(A){if(style_cookie){var B=get_active_stylesheet();set_cookie(style_cookie,B,365)}if(style_cookie_txt){var B=get_active_stylesheet();set_cookie(style_cookie_txt,B,365)}if(style_cookie_site){}};window.onload=function(D){delandbanlinks();checkhighlight();if(document.getElementById("watchedthreads")){var C=new Draggable("watchedthreads",{handle:"watchedthreadsdraghandle",onEnd:function(){E()}});var A=new Resizeable("watchedthreads",{resize:function(){B()}});function E(){set_cookie("watchedthreadstop",document.getElementById("watchedthreads").style.top,30);set_cookie("watchedthreadsleft",document.getElementById("watchedthreads").style.left,30)}function B(){var F=document.getElementById("watchedthreads").offsetWidth;var G=document.getElementById("watchedthreads").offsetHeight;set_cookie("watchedthreadswidth",F,30);set_cookie("watchedthreadsheight",G,30)}}document.onkeydown=keypress};if(style_cookie){var cookie=getCookie(style_cookie);var title=cookie?cookie:get_preferred_stylesheet();set_stylesheet(title)}if(style_cookie_txt){var cookie=getCookie(style_cookie_txt);var title=cookie?cookie:get_preferred_stylesheet();set_stylesheet(title,true)}if(style_cookie_site){var cookie=getCookie(style_cookie_site);var title=cookie?cookie:get_preferred_stylesheet();set_stylesheet(title,false,true)}if(getCookie("kumod")=="yes"){kumod_set=true}