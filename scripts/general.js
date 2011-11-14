function popUp(strURL,strType,strWidth,strHeight) 
  {
    var strOptions="";
    
    if(strType=="console") 
      {
      	strOptions="resizable,height="+strHeight+",width="+strWidth;
      }
      
    if(strType=="fixed") 
      {
      	strOptions="status,height="+strHeight+",width="+strWidth;
      }
      
    if(strType=="elastic") 
      {
      	strOptions="toolbar,menubar,scrollbars,resizable,location,height="+strHeight+",width="+strWidth;
      }
      
    var win = window.open(strURL, 'newWin', strOptions);
    win.opener = self;
    
    return(win);
}