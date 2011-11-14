
/*
 * requires XMLHTTP.js
 * requires $Error.js
 */
 
function Menu(spd) 
  {
	  // Animation of the menu runs on a cycle(speed = spd), with each show/hide instruction 
		// placed on a stack.  Every tick of this clock causes one instruction to be executed.
	  this.startClock(spd || 25);
	} 

Menu.prototype.startClock = function(spd)
  {
	  Menu.prototype.instructionStack = new Array();
	  Menu.prototype.timer = setInterval('Menu.prototype.cycle()',spd);
	}	
	
Menu.prototype.cycle = function()
  {
	  try
		  {
		    // fifo
	      var ob = Menu.prototype.instructionStack.shift();
		    var obs = ob.ob.style;

    		// toggle its state
        if(obs.visibility=='inherit')
	        {
            obs.position='absolute';
            obs.visibility='hidden';
          } 
		    else 
		      {
            obs.position='static';
            obs.visibility='inherit';
          }
			}
		catch(e) {;} 
	}
	
Menu.prototype.addInstruction = function(el)
  {
	  Menu.prototype.instructionStack.push({ob: el});
	}	
	
Menu.prototype.activate = function(el,state,recur)
/* when passed an element reference, will recursively
 * seek children and open them.
 */ 
  {
	  if(el)
	   {
	   	 if(el.style && !recur)
	   	   {
	   	   	 /*
	   	   	  * reset selection when last selected branch
	   	   	  * element loses its focus
	   	   	  */
           this.lastSelectedItem.className = 'deselectedBranchElement';
        
           this.lastSelectedItem = el;

           /*
            * stylize current selection
            */
           el.className = 'selectedBranchElement';
	   	  }
	   	  
		   /*
		    * Change open/closed icon. Will only fire on originally 
		    * clicked control item (which sends the state argument).
		    */
		   if(!state)
			   {
				   try
					   {
					   	 var st = el.getAttribute("iconState");
					   	 var iconInfo = (st == 'open') ? this.getIcon('closed') : this.getIcon('open');
						   el.childNodes.item(0).src = iconInfo.path;
							 el.setAttribute("iconState",(st == 'open') ? 'closed' : 'open');

               /*
                * note that we no longer bother with iconInfo.class, since
                * the className on the element has been set initially, and does
                * not change
                */
						 }
					 catch(e) {}
				 }
				 
	     tempEl=el.nextSibling;
       if(tempEl)
		     {
    	     if(tempEl.nodeType==1)
				     {
						   // add the animation of this element (open/close) to the stack
               this.addInstruction(tempEl);
        	   } 
						this.activate(tempEl,1,1);
    	    }
    	}
  }	
	
Menu.prototype.serialize = function(obj)
  {
	  try
		  {
			  var s = new XMLSerializer();
				var ser = s.serializeToString(obj);
			}
		catch(e)
			{
				var ser = obj.xml;
			}	
		return(ser);
	}

Menu.prototype.load = function(dataFile,container,waitTime)
/*
 * loads the menu's data from dataFile
 * NOTE: assumes dataFile is an XML file.
 * NOTE: container is a string representing an element id
 *
 * Since we cannot load another menu until previous loads are complete,
 * load() will wait until a previous load is finished.
 */
	{
	  var wt = waitTime || 0;
	  if(this.menuLoaded)
		  {
		    // Announce that the menu is loading
		    Menu.prototype.menuLoaded = false; 
		
	      Menu.prototype.container = document.getElementById(container) || document.body;
        // Since the menu will arrive unformatted, lets hide it for now
        Menu.prototype.hideMenu();
		
        this.connection = new XMLHTTP();
        this.connection.loadXML(dataFile,this.buildMenus);
			}
		else if(wt < Menu.prototype.timeout)
		  {
			  setTimeout('Menu.prototype.load("' + dataFile + '","' + container + '",' + (wt+25) + ')',25);
			}
		else // timeout
		  {
			  $Error.alert(new Object(),'The menu has not loaded properly and may behave strangely.');
			}		
  }
  
Menu.prototype.hideMenu = function()
  {
	  Menu.prototype.container.style.visibility = 'hidden';
	}
	
Menu.prototype.showMenu = function()
  {
	  Menu.prototype.container.style.visibility = 'visible';
	}

Menu.prototype.buildMenus = function(re)
/* the callback function which fires when this menu's XML
 * description has been loaded (via this.load).  passes the xml doc ref.
 */
  {		
	  // store the xml representation
		Menu.prototype.XML = re;
		
		// write the DOM representation of the menu
		Menu.prototype.translateToDomTree(Menu.prototype.XML);

		// Make sure the menu insertion was successful and can be referenced 
	  try
		  {
			  /* containerID is the menu's containing xml tag (the id of the root
				 * element of this menu's XML representation.  Icon set and
				 * style sheet properties may be stored here.
				 */
		    var doc = document.getElementById(Menu.prototype.containerID);
			}
		catch(e)
		  {
			  $Error.alert(e,'The menu has not loaded properly and may behave strangely, or not at all.');
				return;
			}
				
		// Announce that the menu has loaded properly
		Menu.prototype.menuLoaded = true; 
		
		// First, see if there is a stylesheet to apply 
	  try
		  {
		    // load the style sheet definition, if any
		    Menu.prototype.loadStyleSheet(doc.attributes.getNamedItem('styleSheet').value);
			}
		catch(e) { ; } // no default style sheet
		
		// Try for a default icon set
		try
		  {
		    // load the icon set definition, if any
		    Menu.prototype.loadIconSet(doc.attributes.getNamedItem('iconSet').value);
			}
		catch(e) { ; } // no default iconset for menu
		
		// show the menu
    Menu.prototype.showMenu();
  }	
	
Menu.prototype.loadStyleSheet = function(file)
  {
		try
		  {
			  var style = document.getElementById(this.styleID);
				style.href = file;
			}
		catch(e)
		  {
        // style object doesn't exist.  create it.
				var head = document.createElement("link");
        head.setAttribute("id",this.styleID);
        head.setAttribute("rel","stylesheet");
        head.setAttribute("type","text/css");
        head.setAttribute("href",file);
        document.getElementsByTagName("head").item(0).appendChild(head);
			}
	}	
	
Menu.prototype.loadIconSet = function(file,waitTime)
/*
 * Load an icon set.  Because the icons cannot be attached until the menu
 * structure exists, we need to continuously call this function until
 * the menu is fully loaded.  
 *
 */
  {
	  var wt = waitTime || 0;
	  if(this.menuLoaded)
		  {
			  // hold further menu actions until icon set is loaded
			  this.menuLoaded = false;
				
        this.connection = new XMLHTTP();
        this.connection.loadXML(file,this.parseIconSet);
			}
		else if(wt < Menu.prototype.timeout)
		  {
			  setTimeout('Menu.prototype.loadIconSet("' + file + '",' + (wt+25) + ')',25);
			}
		else // timeout
		  {
			  $Error.alert(new Object(),'The menu has not loaded properly and may behave strangely.');
			}
	}
	
Menu.prototype.getIcon = function(id)
  {
	  var iconInf = new Object();
	  for(i=0; i<this.iconSet.length; i++)
		  {
			  el = this.iconSet;
				if(el[i].id == id)
					{
						iconInf.path = el[i].location;
						iconInf.path += el[i].fileName;
						iconInf.path += '.' + el[i].extension;
						
						iconInf.className = el[i].className;
						break;
					}
			}
		return(iconInf);
  }
	
Menu.prototype.parseIconSet = function(re)
/*
 * Callback from loadIconSet(). Take the icon set that has been loaded and 
 * create a local representation used by getIcon() to fetch icon definitions
 */
  {
	  var els = re.getElementsByTagName('icon');
		Menu.prototype.iconSet = new Array();
		for(q=0; q<els.length; q++)
			{
			  Menu.prototype.iconSet[Menu.prototype.iconSet.length] = 
				  {
					  id: els.item(q).attributes.getNamedItem('id').value,
						location: els.item(q).attributes.getNamedItem('location').value,
						fileName: els.item(q).attributes.getNamedItem('fileName').value,
						extension: els.item(q).attributes.getNamedItem('extension').value,
						className: els.item(q).attributes.getNamedItem('className').value
					};
			}
    Menu.prototype.attachIcons();
		
		// ok now to continue building menus
		Menu.prototype.menuLoaded = true;
  }	
  
Menu.prototype.setParentHandlers = function(el)
  {
	  /*
	   * check to see if we're on a subElement or rootElement. 
	   * if so, give it the click handlers to 
	   * allow opening/closing branches
	   */
	 if((el.className == 'rootElement') || (el.className == 'subElement'))
	   {
	   	 /*
	   	  * moz (surprisingly) sees childNodes.firstChild as nodeType 3, 
	   	  * while IE sees it (as expected) as nodeType 1.
	   	  * so we handle the referencing slightly differently
	   	  */
	   	 var theRef = (el.firstChild.nodeType == 3) 
	   	            ? el.childNodes.item(1) 
	   	            : el.childNodes.item(0);

	     theRef.onclick = function() { MenuRef.activate(this); }
	   }  	
  }

Menu.prototype.attachIcons = function()
  {
		var d = Menu.prototype.container.getElementsByTagName('div');
		
		for(x=0; x<d.length; x++)
		  {
			  try
				  {
				    var ic = d.item(x).attributes.getNamedItem('icon').value;
				  	var iconInfo = this.getIcon(ic);
				  	
				  	var icon = document.createElement('img');
				  	icon.setAttribute('class',iconInfo.className);
				  	icon.setAttribute('src',iconInfo.path);
				  	
				  	d[x].insertBefore(icon, d[x].firstChild);
					}
				catch(e) {}
				
			  try
				  {
			      var st = d.item(x).attributes.getNamedItem('iconState').value;
				  	var iconInfo = this.getIcon(st);
						
						var control = document.createElement('img');
						control.setAttribute('class',iconInfo.className);
						control.setAttribute('src',iconInfo.path);
						
						d[x].insertBefore(control, d[x].firstChild);
					}
				catch(e) {}
				
        /*
         * this is a hack I'd like some info on.
         *
         * remove this absurd statement and IE fails (style defs disappear)
         * is it just IE? that it works in OPERA and MOZ makes 
         * me think my code (above) is standard, correct, etc.
         */
        d[x].innerHTML = d[x].innerHTML;
        
        // add onclick event to parent nodes
        this.setParentHandlers(d[x]);
      }
  }

Menu.prototype.translateToDomTree = function(nde)
  {
    this.counter = 0; // see this.mapToDom
  	this.menuRoot = null; // init on creation of menu containerID

  	/*
  	 * every node in the original xml doc is sent to this function.
  	 * function will translate valid nodes (nodes with class attribute
  	 * of 'rootElement' || 'subElement' || 'element\) into the proper
  	 * DOM element.  nodes without any of the above class attributes will
  	 * be ignored.
  	 */
  	this.setDomElement = function(node,elParent)
  	  {
  	  	/*
  	  	 * track parent node id for 'subElement' and 'element'
  	  	 * nodes.  'rootElement' nodes always attach to this.menuRoot
  	  	 */
  	  	try
  	  	  {
  	  	    var parentN = document.getElementById(this.nodeIDprefix + elParent.getAttribute(this.nodeIDprefix));
  	  	  } catch(e) {}
  	  	  	
  	  	var nClass = node.getAttribute('class');

  	  	switch(nClass)
  	  	  {
  	  	  	case Menu.prototype.containerID:
  	  	  	
  	  	  	  var nStyle = node.getAttribute('styleSheet');
  	  	  	  var nIcons = node.getAttribute('iconSet');
  	  	  	    	  	  	  
  	  	  	  // '<div id="' + Menu.prototype.containerID + '" styleSheet="' + nStyle + '" iconSet="' + nIcons + '">'
  	  	  	  
  	  	  	  var d = document.createElement('div');
  	  	  	  d.setAttribute("id", Menu.prototype.containerID);
  	  	  	  d.setAttribute("styleSheet", nStyle);
  	  	  	  d.setAttribute("iconSet", nIcons);
  	  	  	  
  	  	  	  this.menuRoot = Menu.prototype.container.appendChild(d);

  	  	  	break;
  	  	  	
  	  	  	case 'rootElement':
  
              var d = this.setBranch(node,nClass);
  	  	  	  var rE = this.menuRoot.appendChild(d);
              var dc = this.setBranchControl(node,nClass);
   	  	  	  var dt = document.createTextNode(node.getAttribute('title'));
  	  	  	  
  	  	  	  dc.appendChild(dt);
   	  	  	  rE.appendChild(dc);
 	  
  	          this.branchStack.add(rE,null);

  	  	  	break;
  	  	  	
  	  	  	case 'subElement':

              var d = this.setBranch(node,nClass);
  	  	  	  var sE = parentN.appendChild(d);
              var dc = this.setBranchControl(node,nClass);
  	  	  	  var dt = document.createTextNode(node.getAttribute('title'));
  	  	  	  
  	  	  	  dc.appendChild(dt);
  	  	  	  sE.appendChild(dc);
  	  	  	  
  	          this.branchStack.add(sE,parentN);

  	  	  	break;
  	  	  	
  	  	  	case 'element':
  	  	  	  
  	  	  	  /*
  	  	  	   * this is CDATA, which may have comments, which may have
  	  	  	   * whitespace, etc. etc. and so may have several children.
  	  	  	   * We therefore loop through all children, and concatenate
  	  	  	   */
  	  	  	  
  	  	  	  var fData = '';
  	  
  	  	  	  for(d=0; d<node.childNodes.length; d++)
  	  	  	    {
		              fData += node.childNodes[d].data;
  	  	  	    }
  	  	  	  
  	  	  	  var data = fData.replace(/[\t\n\r ]+/g, " ");
  	  	  	  
		          if(data.charAt(0) == " ")
		            {
		              data = data.substring(1, data.length);
		            }
		          if(data.charAt(data.length - 1) == " ")
		            {
		              data = data.substring(0, data.length - 1);
		            }
              
              // '<div class="' + nClass + '">' + data + '</div>'
  	  	  	  
  	  	  	  var d = document.createElement('div');
  	  	  	  d.setAttribute("class", nClass);
  	  	  	  d.setAttribute("id", node.getAttribute('id'));
  	  	  	  
  	  	  	  try
  	  	  	    {
  	  	  	    	if(node.getAttribute('icon'))
  	  	  	    	  {
  	  	  	    	    d.setAttribute("icon", node.getAttribute('icon'));
  	  	  	    	  }
  	  	  	    } 
  	  	  	  catch(e){}

  	  	  	  var el = parentN.appendChild(d);
  	  	  	  
  	  	  	  if(data != '')
  	  	  	    {
  	  	  	      el.innerHTML = data;
  	  	  	    }

  	  	  	break;
  	  	  	
  	  	  	default:
  	  	  	break;
  	  	  }
  	  	  
  	  	this.setBranch = function(node,nClass)
  	  	  {
  	  	  	var elem = document.createElement('div');
  	  	  	var nTitle = node.getAttribute('title');
  	  	    elem.setAttribute("id",this.nodeIDprefix + node.getAttribute(this.nodeIDprefix));
 	  	  	  elem.setAttribute(this.nodeTITLEprefix,nTitle);
  	  	  	elem.setAttribute("class", nClass);

  	  	  	return(elem);
  	  	  }
  	  	  
  	  	this.setBranchControl = function(node,nClass)
  	  	  {
  	  	  	var elem = document.createElement('div');
  	  	  	elem.setAttribute("class",nClass + "Control");
  	  	  	elem.onclick = 'menuActivate(this)';
  	  	  	
  	  	    try
  	  	  	  {
  	  	  	   	if(node.getAttribute('iconState'))
  	  	  	  	  {
  	  	  	        elem.setAttribute("iconState", node.getAttribute('iconState'));
  	  	  	        elem.setAttribute("state", node.getAttribute('iconState'));
  	  	  	      }
  	  	  	   	else
  	  	  	   		{
  	  	  	   			elem.setAttribute("state", "closed");
  	  	  	   		}
  	  	  	    		
  	  	  	   	if(node.getAttribute('icon'))
  	  	  	   	  {
  	  	  	   	    elem.setAttribute("icon", node.getAttribute('icon'));
  	  	  	   	  }
  	  	  	  } 
  	  	    catch(e){}  	
  	  	    
  	  	    return(elem);  	  	
  	  	  }
  	  }
  	
  	this.mapToDom = function(node)
  	  {
  	  	/*
  	  	 * there is a slight difference in the way that mozilla and IE will
  	  	 * handle the xml doc.  MOZ, probably correctly, will wrap everything
  	  	 * with a document node, so that root node will have nodeName <#document>,
  	  	 * and a nodeType of 9.
  	  	 *
  	  	 * IE will not do this, and will begin with the first node you will
  	  	 * see in the document.  All of those nodes should have element 
  	  	 * nodeType of 1.
  	  	 *
  	  	 * for our purposes, we snip mozillas #document node.
  	  	 *
  	  	 */
  	  	if(node.nodeType == 9)
  	  	  {
  	  	  	node = node.childNodes[0];
  	  	  }
  	  	
  	  	/*
  	  	 * give nodes an id, so that we can attach
  	  	 * children to it later (see this.setDomElement)
  	  	 */
  	  	node.setAttribute(this.nodeIDprefix,++this.counter);  
  	  	  
        /*
         * build out the dom node
         */
      	this.setDomElement(node,node.parentNode);
		  
		    /*
		     * now handle the chil'un
		     */
			  if(node.hasChildNodes()) 
			    {
		        var cN = node.childNodes;
		     
 					  for(var i=0; i<cN.length; i++)
						  {
						   	if(cN[i].nodeType == 1)
						   	  {
						        this.mapToDom(cN[i]);
						      }
					    }
			    }
      }

    this.mapToDom(nde);

    /*
     * uncomment to see the tree as rendered in the DOM
     */
    //this.showMenuSchema();
    
    // call menuLoaded function, if any
    try
      {
      	menuLoaded();
      }
    catch(e){}
  }
  
// Flagged when the callback function for load() has executed.
// initialized to true so that the first load call is immediately accepted
Menu.prototype.menuLoaded = true;

// The id of the link anchor which contains this menu's style sheet
// This link is dynamically created whenever a stylesheet load is tried.
Menu.prototype.styleID = 'menuStyle';

// This will contain a reference to the menu tree upon successful menu load
Menu.prototype.container = null;

// Set to the id of the root element of this menu's XML representation
Menu.prototype.containerID = 'menuContainer';

// Set to the maximum number of milliseconds any queued load 
// requests will wait before failing
Menu.prototype.timeout = 20000;

// last clicked item
Menu.prototype.lastSelectedItem = new Object();
Menu.prototype.lastSelectedItem.style = true;

/*
 * as used in this.setDomElement:
 *
 * this is the id prefix for created elements, used to track
 * parents for children as they are attached. in final form,
 * the attribute will resemble :: id = "nodeIDprefix_int"
 *
 * note that the root node does not follow this id scheme, 
 * having its id set to whatever Menu.prototype.containerID is.
 *
 * as used in this.mapToDom:
 *
 * this is the actual attribute name added to original xml nodes.
 * ie. <xmlnode ___treePos___= "2">
 */
Menu.prototype.nodeIDprefix = '___treePos___'; 

/*
 * as above, used to write title for node
 */
Menu.prototype.nodeTITLEprefix = '___title___'; 





Menu.prototype.branchStack = 
  {
  	titleStack: new Array(),
  	nodeStack: new Array(),
  	
  	add: function(node,parentNode)
  	  {
  	  	var nd = 
  	  	  {
  	  	  	node: node,
  	  	  	parentNode: parentNode || null,
  	  	  	controlNode: node.firstChild,
  	  	  	state: node.firstChild.getAttribute('state'),
  	  	  	children: new Array()
  	  	  };
  	  	  
  	    this.nodeStack.push(nd);
  	    
  	  	if(parentNode)
  	  	  {
            this.addChild(nd);
  	  	  }
  	  },
  	
  	addChild: function(nd)
  	  {
  	  	for(n=0; n < this.nodeStack.length; n++)
  	  	  {
  	  	  	if(this.nodeStack[n].node === nd.parentNode)
  	  	  	  {
  	  	  	  	this.nodeStack[n].children.push(nd);

  	  	  	  	break;
  	  	  	  }
  	  	  }
  	  }
  };














Menu.prototype.showMenuSchema = function()
  {
  	this.attributeList = new Array
  	  (
  	    'class',
  	    'id',
  	    'iconState',
  	    'state',
  	    'icon',
  	    Menu.prototype.nodeTITLEprefix,
  	    'onclick'
  	  );
  	  
    this.showChildren = function(el, ind)
      {
      	var node = '';
      	var ret = '';
        for(var c = 0;c < el.childNodes.length;++c)
          {
            node = el.childNodes[c];
            if(node.nodeType == 1)
              {
                ret += ind+this.nodeStart(node);
                ret += this.showChildren(node, (ind+'\t'));
                ret += this.nodeEnd(node, ind);
              }
          }
        return(ret);
      }

    this.nodeStart = function(el)
      { 
      	var ret = '';
	      ret += '&lt;' + el.nodeName;
	      ret += ((el.type)?(' type=\"' + el.type+'\"'):'');
	      ret += this.getAttributeString(el);
        ret += (el.childNodes.length ? '&gt\n' : ' /&gt\n');
        
        return(ret);
      }

    this.nodeEnd = function(el, ind)
      {
        return (el.childNodes.length ? (ind+'&lt;/' + el.nodeName +'&gt\n') : '');
      }
  	
    this.getAttributeString = function(el)
		  {
		  	atts = ' ';
		  	var aL = this.attributeList;
		  	
		  	for(a=0; a < aL.length; a++)
		  	  {
				  	if(el.getAttribute(aL[a]))
				  	  {
				  	    atts += aL[a] +'="' + el.getAttribute(aL[a]) + '" ';
				  	  }
		  	  }

		  	return(atts);
		  }
  	
    var div = document.getElementById('menuContainer');
    
    var sc = '<pre>';
    sc += this.nodeStart(div);
    sc += this.showChildren(div, '\t');
    sc += this.nodeEnd(div, '');
    sc += '<\/pre>';
    
    var schema = document.createElement('div');
    schema.setAttribute('class','schema');
    
    document.body.appendChild(schema).innerHTML = sc;
  };







