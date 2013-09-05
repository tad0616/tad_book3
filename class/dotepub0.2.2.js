/* 

DOTEPUB.COM
http://dotepub.com/j/dotepub.js

Copyright 2010 Xavier Badosa (xavierbadosa.com)

Licensed under the Apache License, Version 2.0 (the "License"); 
you may not use this file except in compliance with the License. 
You may obtain a copy of the License at 

	http://www.apache.org/licenses/LICENSE-2.0 

Unless required by applicable law or agreed to in writing, software 
distributed under the License is distributed on an "AS IS" BASIS, 
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express 
or implied. See the License for the specific language governing 
permissions and limitations under the License. 

Readability functions:

Copyright (c) 2010 Arc90 Inc, 
modified by Xavier Badosa

*/

var debug=false;
var dotEPUB = {
	version: '0.2.2',
	doc: null,
	flags: "|",
	path: "http://dotepub.com/api/v1/post",

	/** AUX FUNCTIONS **/

	/* Simplified from getElementsByClassName by Jonathan Snook + Robert Nyman */
	gelByClass: function(oElm, strClassName) {
		var arrElements = oElm.getElementsByTagName("*");
		var arrReturnElements = new Array();
		strClassName = strClassName.replace(/\-/g, "\\-");
		var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
		var oElement;
		for(var i=0; i<arrElements.length; i++){
			oElement = arrElements[i];      
			if(oRegExp.test(oElement.className)){
				arrReturnElements.push(oElement);
			}   
		}
		return (arrReturnElements);
	},

	meta: function(m) {
		var meta=document.getElementsByTagName("meta");
		for(var i=0; i<meta.length; i++){
			if(meta[i].getAttribute("name") && meta[i].getAttribute("name").toLowerCase()==m){
				return meta[i].getAttribute("content");
			}
		}
		return "";
	},

	getAuthor: function(m){
		if(document.getElementById("dotEPUBauthor")){
			return "<dotEPUB_sep/>"+readability.getInnerText(document.getElementById("dotEPUBauthor"));
		}

		/* byline-author blogger */
		/* byline-name bbc*/
		/* byline blogger nyt bbc salon */
		/* headline_meta postmeta postmetadata entry-tagline wordpress */
		/* blog-byline theguardian blogs */
		/* firma elpais.com */
		/* article_byline pw */
		/* No: "author" too wide: will get comments' author*/
		var classes=["byline-author", "byline-name", "byline", "headline_meta", "postmeta", "postmetadata", "entry-tagline", "blog-byline", "firma", "article_byline"];

		/*meta movabletype cnn... drupal... */
		var metaauthor=dotEPUB.meta("author");

		/* post-author blogger */
		var author=dotEPUB.gelByClass(document, "post-author")[0];
		if (typeof author!="undefined"){
			var fn=dotEPUB.gelByClass(author, "fn")[0];
			if(typeof fn!="undefined"){
				return metaauthor+"<dotEPUB_sep/>"+readability.getInnerText(fn);
			}
			return metaauthor+"<dotEPUB_sep/>"+readability.getInnerText(author);
		}

		for(var c=0; c<classes.length; c++){
			author=dotEPUB.gelByClass(document, classes[c])[0];
			if (typeof author!="undefined"){
				return metaauthor+"<dotEPUB_sep/>"+readability.getInnerText(author);
			}
		}
		return metaauthor;
	},

    addFlag: function(flag) {
        dotEPUB.flags = dotEPUB.flags+flag+"|";
    },

	field: function(e,name,value){
		var hidden = document.createElement("input");
		hidden.setAttribute("type", "hidden");
		hidden.setAttribute("name", name);
		hidden.setAttribute("value", value);
		e.appendChild(hidden);
	},

	send: function(article,author,copy){
			if(article.content.length>300000){
			var confirmation="This page is very long!\n\nThe processing and downloading of the e-book may take some time (please be patient) and some e-readers may be unable to view it.\n\nDo you want to proceed?";
			switch(dotEPUB_lang){
					case "es":
						confirmation="¡Esta página es muy larga!\n\nEl procesamiento y descarga del libro electrónico puede durar un rato (por favor, sé paciente) y algunos e-readers quizás no sean capaces de visualizarlo.\n\n¿Deseas continuar de todas formas?"
						break;
					case "ca":
						confirmation="Aquesta pàgina és molt llarga!\n\nEl processament i descàrrega del llibre electrònic pot trigar una estona (sisplau, sigues pacient) i alguns e-readers potser no siguin capaços de visualitzar-lo.\n\nVols continuar de totes formes?"
						break;
				}

				if(!confirm(confirmation)){
					return;
				}
			}

			/*Send info to server */
			var form = document.createElement("form");
			form.setAttribute("action", dotEPUB.path);
			form.setAttribute("method", "post");
			form.setAttribute("accept-charset", "utf-8");

			dotEPUB.field(form,"title",article.title); //document.title
			dotEPUB.field(form,"html",article.content);
			dotEPUB.field(form,"url",document.location.href);
			dotEPUB.field(form,"author",author);
			dotEPUB.field(form,"copy",copy);
			dotEPUB.field(form,"flags",dotEPUB.flags);
			dotEPUB.field(form,"links",dotEPUB_links);
			dotEPUB.field(form,"lang",dotEPUB_lang);

			document.body.appendChild(form);
			form.submit();
			form.parentNode.removeChild(form);
	},

	escapePre: function(){
		var pre=dotEPUB.doc.getElementsByTagName("pre");
		for(var i=0; i<pre.length; i++){
			pre[i].innerHTML = pre[i].innerHTML.replace(/\n/g,'{_dotepub_cr_}').replace(/ /g,'{_dotepub_sp_}');
		}
		if (pre.length>0){
			dotEPUB.addFlag("pre");
		}
	},

	init: function(){
        if(window.location.host!="dotepub.com" && (window.location.protocol + "//" + window.location.host + "/") == window.location.href){
			switch(dotEPUB_lang){
				case "es":
					alert("dotEPUB.com está diseñado para procesar páginas de artículos, entradas de blogs, relatos, etc., no páginas principales.");
					break;
				case "ca":
					alert("dotEPUB.com està dissenyat per processar pàgines d'articles, entrades de blogs, relats, etc., no pàgines principals.");
					break;
				default:
					alert("dotEPUB.com is designed to process article pages, blog entries, stories, etc., not home pages.");
			}
			dotEPUBstatus.parentNode.removeChild(dotEPUBstatus);
			return;
		}

		/* Cloning */
		dotEPUB.doc = document.createElement("html");
		var doccont=document.getElementsByTagName('html')[0];
		var clonecont=doccont.cloneNode(true);
		dotEPUB.doc.appendChild(clonecont);

		/* Cleaning the cloned document */
		dotEPUB.escapePre();
		var article=readability.init();
		if (article.content==""){
			switch(dotEPUB_lang){
				case "es":
					alert("dotEPUB.com no es capaz de procesar esta página. Si es una página de un artículo, una entrada de blog, un relato o similar, informa de este problema en http://code.google.com/p/dotepub/.");
					break;
				case "ca":
					alert("dotEPUB.com no és capaç de processar aquesta pàgina. Si és una pàgina d'un article, una entrada de blog, un relat o similar, informa d'aquest problema a http://code.google.com/p/dotepub/.");
					break;
				default:
					alert("dotEPUB.com is unable to process this page. If it's an article page, a blog entry, a story, etc. report this problem at http://code.google.com/p/dotepub/.");
			}
		}else{
			article.title = (document.getElementById("dotEPUBtitle")) ? readability.getInnerText(document.getElementById("dotEPUBtitle")) : article.title;
			dotEPUB.send(article, dotEPUB.getAuthor(), dotEPUB.meta("copyright"));
		}
		dotEPUBstatus.parentNode.removeChild(dotEPUBstatus);
	}
}

var dbg = (debug && typeof console !== 'undefined') ? function(s) {
    console.log("Readability: " + s);
} : function() {};

/*
 * Readability. An Arc90 Lab Experiment. 
 * Website: http://lab.arc90.com/experiments/readability
 * Source:  http://code.google.com/p/arc90labs-readability
 *
 * "Readability" is a trademark of Arc90 Inc and may not be used without explicit permission. 
 *
 * Copyright (c) 2010 Arc90 Inc
 * Readability is licensed under the Apache License, Version 2.0.
**/
var readability = {
    version:                '1.6.2-1.7.0 + dotEPUB mod',
/*    emailSrc:               'http://lab.arc90.com/experiments/readability/email.php',*/
    iframeLoads:             0,
/*    convertLinksToFootnotes: false,*/
    frameHack:               false, /**
                                      * The frame hack is to workaround a firefox bug where if you
                                      * pull content out of a frame and stick it into the parent element, the scrollbar won't appear.
                                      * So we fake a scrollbar in the wrapping div.
                                     **/
    biggestFrame:            false,
    bodyCache:               null,   /* Cache the body HTML in case we need to re-use it later */
    flags:                   0x1 | 0x2 | 0x4,   /* Start with both flags set. */
    
    /* constants */
    FLAG_STRIP_UNLIKELYS: 0x1,
    FLAG_WEIGHT_CLASSES:  0x2,
	FLAG_CLEAN_CONDITIONALLY: 0x4,
    
    /**
     * All of the regular expressions in use within readability.
     * Defined up here so we don't instantiate them repeatedly in loops.
     **/
    regexps: {
        unlikelyCandidatesRe: /combx|comment|disqus|extra|foot|header|menu|rss|shoutbox|sidebar|sponsor|ad-break|agegate|pagination|pager|popup/i, // updated from 1.7.0 (1.6.2 was: /combx|comment|disqus|foot|header|menu|rss|shoutbox|sidebar|sponsor|ad-break|agegate/i)
		okMaybeItsACandidateRe: /and|article|body|column|main|shadow/i, // updated from 1.7.0 (1.6.2 was: /and|article|body|column|main/i)
		positiveRe: /article|body|content|entry|hentry|main|page|pagination|post|text|blog|story|dotEPUBcontent/i, // updated from 1.7.0 (1.6.2 was: /article|body|content|entry|hentry|page|pagination|post|text|blog|story/i)
		negativeRe: /combx|comment|com-|contact|foot|footer|footnote|masthead|media|meta|outbrain|promo|related|scroll|shoutbox|sidebar|sponsor|shopping|tags|widget|utilidades|votos|coment|dablink|thumb|share|complementa|image|cbw|dotEPUBremove/i,// updated from 1.7.0 (1.6.2 was: /combx|comment|contact|foot|footer|footnote|masthead|media|meta|promo|related|scroll|shoutbox|sponsor|tags|widget|utilidades|votos|coment|dablink|thumb|share|complementa/i, //dotEPUB: added utilidades|votos|coment|editsection|article_related|dablink|thumb|share|info_complementa|caption|articleSpanImage|inlineImage
		divToPElementsRe:       /<(a|blockquote|dl|div|img|ol|p|pre|table|ul)/i,
        replaceBrsRe:           /(<br[^>]*>[ \n\r\t]*){2,}/gi,
        replaceFontsRe:         /<(\/?)font[^>]*>/gi,
        trimRe:                 /^\s+|\s+$/g,
        normalizeRe:            /\s{2,}/g,
        killBreaksRe:           /(<br\s*\/?>(\s|&nbsp;?)*){1,}/g,
        videoRe:                /http:\/\/(www\.)?(youtube|vimeo)\.com/i
		/*	,
        skipFootnoteLinkRe:     /^\s*(\[?[a-z0-9]{1,2}\]?|^|edit|citation needed)\s*$/i*/
    },

	/**
	 * Runs readability.
	 * 
	 * Workflow:
	 *  1. Prep the document by removing script tags, css, etc.
	 *  2. Build readability's DOM tree.
	 *  3. Grab the article content from the current dom tree.
	 *  4. Replace the current DOM tree with the new one.
	 *  5. Read peacefully.
	 *
	 * @return void
	 **/
	init: function() {
        /* Before we do anything, remove all scripts that are not readability. */
		window.onload = window.onunload = function() {};
		var scripts = dotEPUB.doc.getElementsByTagName('script');
		for(var i = scripts.length-1; i >= 0; i--)
		{
			if(typeof(scripts[i].src) == "undefined" || (scripts[i].src.indexOf('readability') == -1))
			{
				scripts[i].nodeValue="";
				scripts[i].removeAttribute('src');
			    scripts[i].parentNode.removeChild(scripts[i]);          
			}
		}


		if(dotEPUB.doc.getElementsByTagName('body')[0] && !readability.bodyCache)
			readability.bodyCache = dotEPUB.doc.getElementsByTagName('body')[0].innerHTML;
		
		readability.prepDocument();

		var articleTitle   = readability.getArticleTitle();
		var articleContent = readability.grabArticle();


		/**
         * If we attempted to strip unlikely candidates on the first run through, and we ended up with no content,
         * that may mean we stripped out the actual content so we couldn't parse it. So re-run init while preserving
         * unlikely candidates to have a better shot at getting our content out properly.
        **/
        /**
         * If we attempted to strip unlikely candidates on the first run through, and we ended up with no content,
         * that may mean we stripped out the actual content so we couldn't parse it. So re-run init while preserving
         * unlikely candidates to have a better shot at getting our content out properly.
        **/
        if(readability.getInnerText(articleContent, false).length < 250)
        {
            if (readability.flagIsActive(readability.FLAG_STRIP_UNLIKELYS)) {
                readability.removeFlag(readability.FLAG_STRIP_UNLIKELYS);
                dotEPUB.doc.getElementsByTagName('body')[0].innerHTML = readability.bodyCache;
                return readability.init();
            }
            else if (readability.flagIsActive(readability.FLAG_WEIGHT_CLASSES)) {
                readability.removeFlag(readability.FLAG_WEIGHT_CLASSES);
                dotEPUB.doc.getElementsByTagName('body')[0].innerHTML = readability.bodyCache;
                return readability.init();              
            }
			else if (readability.flagIsActive(readability.FLAG_CLEAN_CONDITIONALLY)) {
				readability.removeFlag(readability.FLAG_CLEAN_CONDITIONALLY);
				dotEPUB.doc.getElementsByTagName('body')[0].innerHTML = readability.bodyCache;
				return readability.init();
			}
            else {
                articleContent.innerHTML = ""; //dotepub
            }
        }

		/* dotepub */
		return {title:articleTitle.innerHTML, content:articleContent.innerHTML};
	},

    /**
     * Get the article title as an H1.
     *
     * @return void
     **/
    getArticleTitle: function () {
        var curTitle = "",
            origTitle = "";

			/* dotEPUB Simplified */
			curTitle = origTitle = readability.getInnerText(dotEPUB.doc.getElementsByTagName('title')[0]);             
        
        if(curTitle.match(/ [\|\-] /))
        {
            curTitle = origTitle.replace(/(.*)[\|\-] .*/gi,'$1');
            
            if(curTitle.split(' ').length < 3) {
                curTitle = origTitle.replace(/[^\|\-]*[\|\-](.*)/gi,'$1');
            }
        }
        else if(curTitle.indexOf(': ') !== -1)
        {
            curTitle = origTitle.replace(/.*:(.*)/gi, '$1');

            if(curTitle.split(' ').length < 3) {
                curTitle = origTitle.replace(/[^:]*[:](.*)/gi,'$1');
            }
        }
        else if(curTitle.length > 150 || curTitle.length < 15)
        {
            var hOnes = dotEPUB.doc.getElementsByTagName('h1');
            if(hOnes.length == 1)
            {
                curTitle = readability.getInnerText(hOnes[0]);
            }
        }

        curTitle = curTitle.replace( readability.regexps.trimRe, "" );

        if(curTitle.split(' ').length <= 4) {
            curTitle = origTitle;
        }
        
        var articleTitle = document.createElement("H1");
        articleTitle.innerHTML = curTitle;
        
        return articleTitle;
    },

	/**
	 * Prepare the HTML document for readability to scrape it.
	 * This includes things like stripping javascript, CSS, and handling terrible markup.
	 * 
	 * @return void
	 **/
	prepDocument: function () {
		/**
		 * In some cases a body element can't be found (if the HTML is totally hosed for example)
		 * so we create a new body node and append it to the document.
		 */
		if(dotEPUB.doc.getElementsByTagName('body')[0] === null)
		{
			var body = document.createElement("body");
			try {
				dotEPUB.doc.getElementsByTagName('body')[0] = body;		
			}
			catch(e) {
				dotEPUB.doc.documentElement.appendChild(body);
                dbg(e);
			}
		}

		var frames = dotEPUB.doc.getElementsByTagName('frame');
		if(frames.length > 0)
		{
            var bestFrame = null;
            var bestFrameSize = 0;    /* The frame to try to run readability upon. Must be on same domain. */
			var biggestFrameSize = 0; /* Used for the error message. Can be on any domain. */
            for(var frameIndex = 0; frameIndex < frames.length; frameIndex++)
            {
                var frameSize = frames[frameIndex].offsetWidth + frames[frameIndex].offsetHeight;
                var canAccessFrame = false;
                try {
					frames[frameIndex].contentWindow.dotEPUB.doc.getElementsByTagName('body')[0];
					canAccessFrame = true;
                }
                catch(eFrames) {
                    dbg(eFrames);
                }

				if(frameSize > biggestFrameSize) {
					biggestFrameSize         = frameSize;
					readability.biggestFrame = frames[frameIndex];
				}
                
                if(canAccessFrame && frameSize > bestFrameSize)
                {
                    readability.frameHack = true;
    
                    bestFrame = frames[frameIndex];
                    bestFrameSize = frameSize;
                }
            }
					
			if(bestFrame)
			{
				var newBody = document.createElement('body');
				newBody.innerHTML = bestFrame.contentWindow.dotEPUB.doc.getElementsByTagName('body')[0].innerHTML;
				newBody.style.overflow = 'scroll';
				dotEPUB.doc.getElementsByTagName('body')[0] = newBody;
				
				var frameset = dotEPUB.doc.getElementsByTagName('frameset')[0];
				if(frameset) {
					frameset.parentNode.removeChild(frameset); }

			}
		}

		/* remove all stylesheets 
		for (var k=0;k < dotEPUB.doc.getElementsByTagName('style').length; k++) {
			if (dotEPUB.doc.getElementsByTagName('style')[k].href != null && dotEPUB.doc.getElementsByTagName('style')[k].href.lastIndexOf("readability") == -1) {
				dotEPUB.doc.getElementsByTagName('style')[k].disabled = true;
			}
		}*/

        /* Remove all style tags in head (not doing this on IE) - TODO: Why not? */
        var styleTags = dotEPUB.doc.getElementsByTagName("style");
        for (var st=0;st < styleTags.length; st++) {
            if (navigator.appName != "Microsoft Internet Explorer") {
                styleTags[st].textContent = ""; }
        }

		/* Turn all double br's into p's */
		/* Note, this is pretty costly as far as processing goes. Maybe optimize later. */
		dotEPUB.doc.getElementsByTagName('body')[0].innerHTML = dotEPUB.doc.getElementsByTagName('body')[0].innerHTML.replace(readability.regexps.replaceBrsRe, '</p><p>').replace(readability.regexps.replaceFontsRe, '<$1span>');

	},

    /**
     * Prepare the article node for display. Clean out any inline styles,
     * iframes, forms, strip extraneous <p> tags, etc.
     *
     * @param Element
     * @return void
     **/
    prepArticle: function (articleContent) {
        readability.cleanStyles(articleContent);
        readability.killBreaks(articleContent);

        /* Clean out junk from the article content */
        readability.cleanConditionally(articleContent, "form");
        readability.clean(articleContent, "object");
        /* readability.clean(articleContent, "h1"); dotEPUB cancelled */

        /**
         * If there is only one h2, they are probably using it
         * as a header and not a subheader, so remove it since we already have a header.

		dotEPUB cancelled 
        
		if(articleContent.getElementsByTagName('h2').length == 1) {
            readability.clean(articleContent, "h2"); }
*/

		readability.clean(articleContent, "iframe");
        readability.cleanHeaders(articleContent);

        /* Do these last as the previous stuff may have removed junk that will affect these */
        readability.cleanConditionally(articleContent, "table");
        readability.cleanConditionally(articleContent, "ul");
        readability.cleanConditionally(articleContent, "div");

        /* Remove extra paragraphs */
        var articleParagraphs = articleContent.getElementsByTagName('p');
        for(var i = articleParagraphs.length-1; i >= 0; i--)
        {
            var imgCount    = articleParagraphs[i].getElementsByTagName('img').length;
            var embedCount  = articleParagraphs[i].getElementsByTagName('embed').length;
            var objectCount = articleParagraphs[i].getElementsByTagName('object').length;
            
            if(imgCount === 0 && embedCount === 0 && objectCount === 0 && readability.getInnerText(articleParagraphs[i], false) == '')
            {
                articleParagraphs[i].parentNode.removeChild(articleParagraphs[i]);
            }
        }

        try {
            articleContent.innerHTML = articleContent.innerHTML.replace(/<br[^>]*>\s*<p/gi, '<p');      
        }
        catch (e) {
            dbg("Cleaning innerHTML of breaks failed. This is an IE strict-block-elements bug. Ignoring.: " + e);
        }
    },
	
    /**
     * Initialize a node with the readability object. Also checks the
     * className/id for special names to add to its score.
     *
     * @param Element
     * @return void
    **/
    initializeNode: function (node) {
        node.readability = {"contentScore": 0};         

        switch(node.tagName) {
            case 'DIV':
                node.readability.contentScore += 5;
                break;

            case 'PRE':
            case 'TD':
            case 'BLOCKQUOTE':
                node.readability.contentScore += 3;
                break;
                
            case 'ADDRESS':
            case 'OL':
            case 'UL':
            case 'DL':
            case 'DD':
            case 'DT':
            case 'LI':
            case 'FORM':
                node.readability.contentScore -= 3;
                break;

            case 'H1':
            case 'H2':
            case 'H3':
            case 'H4':
            case 'H5':
            case 'H6':
            case 'TH':
                node.readability.contentScore -= 5;
                break;
        }
       
        node.readability.contentScore += readability.getClassWeight(node);
    },
	
    /***
     * grabArticle - Using a variety of metrics (content score, classname, element types), find the content that is
     *               most likely to be the stuff a user wants to read. Then return it wrapped up in a div.
     *
     * @return Element
    **/
    grabArticle: function () {
        var stripUnlikelyCandidates = readability.flagIsActive(readability.FLAG_STRIP_UNLIKELYS);

        /**
         * First, node prepping. Trash nodes that look cruddy (like ones with the class name "comment", etc), and turn divs
         * into P tags where they have been used inappropriately (as in, where they contain no other block level elements.)
         *
         * Note: Assignment from index for performance. See http://www.peachpit.com/articles/article.aspx?p=31567&seqNum=5
         * TODO: Shouldn't this be a reverse traversal?
        **/
        var node = null;
        var nodesToScore = [];
        for(var nodeIndex = 0; (node = dotEPUB.doc.getElementsByTagName('body')[0].getElementsByTagName('*')[nodeIndex]); nodeIndex++)//dotEPUB updated
        {
            /* Remove unlikely candidates */
            if (stripUnlikelyCandidates) {
                var unlikelyMatchString = node.className + node.id;
                if (
					unlikelyMatchString.search(readability.regexps.unlikelyCandidatesRe) !== -1 &&
                   	unlikelyMatchString.search(readability.regexps.okMaybeItsACandidateRe) == -1 &&
                   	node.tagName !== "BODY"
				)
                {
                    dbg("Removing unlikely candidate - " + unlikelyMatchString);
                    node.parentNode.removeChild(node);
                    nodeIndex--;
                    continue;
                }               
            }

            if (node.tagName === "P" || node.tagName === "TD" || node.tagName === "PRE") {
                nodesToScore[nodesToScore.length] = node;
            }

            /* Turn all divs that don't have children block level elements into p's */
            if (node.tagName === "DIV") {
                if (node.innerHTML.search(readability.regexps.divToPElementsRe) === -1) {
                    dbg("Altering div to p");
                    var newNode = document.createElement('p');
                    try {
                        newNode.innerHTML = node.innerHTML;             
                        node.parentNode.replaceChild(newNode, node);
                        nodeIndex--;

                        nodesToScore[nodesToScore.length] = node;
                    }
                    catch(e) {
                        dbg("Could not alter div to p, probably an IE restriction, reverting back to div.: " + e);
                    }
                }
                /*else
                {
                    // EXPERIMENTAL dotEPUB: removed completely
                    for(var i = 0, il = node.childNodes.length; i < il; i++) {
                        var childNode = node.childNodes[i];
                        if(childNode.nodeType == 3) { // Node.TEXT_NODE
                            dbg("replacing text node with a p (dotEPUB: span) tag with the same content.");
                            var p = document.createElement('span'); //dotEPUB: p -> span
							p.innerHTML = childNode.nodeValue;
                            p.style.display = 'inline';
                            p.className = 'readability-styled';
                            childNode.parentNode.replaceChild(p, childNode);
                        }
                    }
                }*/
            } 
        }

        /**
         * Loop through all paragraphs, and assign a score to them based on how content-y they look.
         * Then add their score to their parent node.
         *
         * A score is determined by things like number of commas, class names, etc. Maybe eventually link density.
        **/
        var candidates = [];
        for (var pt=0; pt < nodesToScore.length; pt++) {
            var parentNode      = nodesToScore[pt].parentNode;
            var grandParentNode = parentNode ? parentNode.parentNode : null;
            var innerText       = readability.getInnerText(nodesToScore[pt]);

            if(!parentNode || typeof(parentNode.tagName) == 'undefined') {
                continue;
            }

            /* If this paragraph is less than 25 characters, don't even count it. */
            if(innerText.length < 25) {
                continue; }

            /* Initialize readability data for the parent. */
            if(typeof parentNode.readability == 'undefined') 
            {
                readability.initializeNode(parentNode);
                candidates.push(parentNode);
            }

            /* Initialize readability data for the grandparent. */
            if(grandParentNode && typeof(grandParentNode.readability) == 'undefined' && typeof(grandParentNode.tagName) != 'undefined')
            {
                readability.initializeNode(grandParentNode);
                candidates.push(grandParentNode);
            }

            var contentScore = 0;

            /* Add a point for the paragraph itself as a base. */
            contentScore++;

            /* Add points for any commas within this paragraph */
            contentScore += innerText.split(',').length;
            
            /* For every 100 characters in this paragraph, add another point. Up to 3 points. */
            contentScore += Math.min(Math.floor(innerText.length / 100), 3);
            
            /* Add the score to the parent. The grandparent gets half. */
            parentNode.readability.contentScore += contentScore;

            if(grandParentNode) {
                grandParentNode.readability.contentScore += contentScore/2;             
            }
        }

        /**
         * After we've calculated scores, loop through all of the possible candidate nodes we found
         * and find the one with the highest score.
        **/
        var topCandidate = null;
        for(var c=0, cl=candidates.length; c < cl; c++)
        {
            /**
             * Scale the final candidates score based on link density. Good content should have a
             * relatively small link density (5% or less) and be mostly unaffected by this operation.
            **/
            candidates[c].readability.contentScore = candidates[c].readability.contentScore * (1-readability.getLinkDensity(candidates[c]));

            dbg('Candidate: ' + candidates[c] + " (" + candidates[c].className + ":" + candidates[c].id + ") with score " + candidates[c].readability.contentScore);

            if(!topCandidate || candidates[c].readability.contentScore > topCandidate.readability.contentScore) {
                topCandidate = candidates[c]; }
        }

        /**
         * If we still have no top candidate, just use the body as a last resort.
         * We also have to copy the body node so it is something we can modify.
         **/
        if (topCandidate === null || topCandidate.tagName == "BODY")
        {
            topCandidate = document.createElement("DIV");
            topCandidate.innerHTML = dotEPUB.doc.getElementsByTagName('body')[0].innerHTML;
            dotEPUB.doc.getElementsByTagName('body')[0].innerHTML = "";
            dotEPUB.doc.getElementsByTagName('body')[0].appendChild(topCandidate);
            readability.initializeNode(topCandidate);
        }

        /**
         * Now that we have the top candidate, look through its siblings for content that might also be related.
         * Things like preambles, content split by ads that we removed, etc.
        **/
        var articleContent        = document.createElement("DIV");
            articleContent.id     = "readability-content";
        var siblingScoreThreshold = Math.max(10, topCandidate.readability.contentScore * 0.2);
        var siblingNodes          = topCandidate.parentNode.childNodes;


        for(var s=0, sl=siblingNodes.length; s < sl; s++)
        {
            var siblingNode = siblingNodes[s];
            var append      = false;

            dbg("Looking at sibling node: " + siblingNode + " (" + siblingNode.className + ":" + siblingNode.id + ")" + ((typeof siblingNode.readability != 'undefined') ? (" with score " + siblingNode.readability.contentScore) : ''));
            dbg("Sibling has score " + (siblingNode.readability ? siblingNode.readability.contentScore : 'Unknown'));

            if(siblingNode === topCandidate)
            {
                append = true;
            }

            var contentBonus = 0;
            /* Give a bonus if sibling nodes and top candidates have the example same classname */
            if(siblingNode.className == topCandidate.className && topCandidate.className != "") {
                contentBonus += topCandidate.readability.contentScore * 0.2;
            }

            if(typeof siblingNode.readability != 'undefined' && (siblingNode.readability.contentScore+contentBonus) >= siblingScoreThreshold)
            {
                append = true;
            }
            
            if(siblingNode.nodeName == "P") {
                var linkDensity = readability.getLinkDensity(siblingNode);
                var nodeContent = readability.getInnerText(siblingNode);
                var nodeLength  = nodeContent.length;
                
                if(nodeLength > 80 && linkDensity < 0.25)
                {
                    append = true;
                }
                else if(nodeLength < 80 && linkDensity === 0 && nodeContent.search(/\.( |$)/) !== -1)
                {
                    append = true;
                }
            }

            if(append)
            {
                dbg("Appending node: " + siblingNode);

                var nodeToAppend = null;
                if(siblingNode.nodeName != "DIV" && siblingNode.nodeName != "P") {
                    /* We have a node that isn't a common block level element, like a form or td tag. Turn it into a div so it doesn't get filtered out later by accident. */
                    
                    dbg("Altering siblingNode of " + siblingNode.nodeName + ' to div.');
                    nodeToAppend = document.createElement('div');
                    try {
                        nodeToAppend.id = siblingNode.id;
                        nodeToAppend.innerHTML = siblingNode.innerHTML;
                    }
                    catch(e)
                    {
                        dbg("Could not alter siblingNode to div, probably an IE restriction, reverting back to original.");
                        nodeToAppend = siblingNode;
                        s--;
                        sl--;
                    }
                } else {
                    nodeToAppend = siblingNode;
                    s--;
                    sl--;
                }
                
                /* To ensure a node does not interfere with readability styles, remove its classnames */
                nodeToAppend.className = "";

                /* Append sibling and subtract from our list because it removes the node when you append to another node */
                articleContent.appendChild(nodeToAppend);
            }
        }


        /**
         * So we have all of the content that we need. Now we clean it up for presentation.
        **/
        readability.prepArticle(articleContent);
        
        return articleContent;
    },
	
    /**
     * Get the inner text of a node - cross browser compatibly.
     * This also strips out any excess whitespace to be found.
     *
     * @param Element
     * @return string
    **/
    getInnerText: function (e, normalizeSpaces) {
        var textContent    = "";

		if(typeof(e.textContent) == "undefined" && typeof(e.innerText) == "undefined") {
			return "";
		}

        normalizeSpaces = (typeof normalizeSpaces == 'undefined') ? true : normalizeSpaces;

        if (navigator.appName == "Microsoft Internet Explorer") {
            textContent = e.innerText.replace( readability.regexps.trimRe, "" ); }
        else {
            textContent = e.textContent.replace( readability.regexps.trimRe, "" ); }

        if(normalizeSpaces) {
            return textContent.replace( readability.regexps.normalizeRe, " "); }
        else {
            return textContent; }
    },

    /**
     * Get the number of times a string s appears in the node e.
     *
     * @param Element
     * @param string - what to split on. Default is ","
     * @return number (integer)
    **/
    getCharCount: function (e,s) {
        s = s || ",";
        return readability.getInnerText(e).split(s).length-1;
    },

    /**
     * Remove the style attribute on every e and under.
     * TODO: Test if getElementsByTagName(*) is faster.
     *
     * @param Element
     * @return void
    **/
    cleanStyles: function (e) {
        e = e || doc;
        var cur = e.firstChild;

        if(!e) {
            return; }

        // Remove any root styles, if we're able.
        if(typeof e.removeAttribute == 'function' && e.className != 'readability-styled') {
            e.removeAttribute('style'); }

        // Go until there are no more child nodes
        while ( cur !== null ) {
            if ( cur.nodeType == 1 ) {
                // Remove style attribute(s) :
                if(cur.className != "readability-styled") {
                    cur.removeAttribute("style");                   
                }
                readability.cleanStyles( cur );
            }
            cur = cur.nextSibling;
        }           
    },

	/**
	 * Get the density of links as a percentage of the content
	 * This is the amount of text that is inside a link divided by the total text in the node.
	 * 
	 * @param Element
	 * @return number (float)
	**/
	getLinkDensity: function (e) {
		var links      = e.getElementsByTagName("a");
		var textLength = readability.getInnerText(e).length;
		var linkLength = 0;
		for(var i=0, il=links.length; i<il;i++)
		{
			linkLength += readability.getInnerText(links[i]).length;
		}		

		return linkLength / textLength;
	},
	
	    /**
     * Get an elements class/id weight. Uses regular expressions to tell if this 
     * element looks good or bad.
     *
     * @param Element
     * @return number (Integer)
    **/
    getClassWeight: function (e) {
        if(!readability.flagIsActive(readability.FLAG_WEIGHT_CLASSES)) {
            return 0;
        }

        var weight = 0;

        /* Look for a special classname */
        if (typeof(e.className) === 'string' && e.className != '')
        {
            if(e.className.search(readability.regexps.negativeRe) !== -1) {
                weight -= 25; }

            if(e.className.search(readability.regexps.positiveRe) !== -1) {
                weight += 25; }
        }

        /* Look for a special ID */
        if (typeof(e.id) === 'string' && e.id != '')
        {
            if(e.id.search(readability.regexps.negativeRe) !== -1) {
                weight -= 25; }

            if(e.id.search(readability.regexps.positiveRe) !== -1) {
                weight += 25; }
        }

        return weight;
    },

	nodeIsVisible: function (node) {
		return (node.offsetWidth !== 0 || node.offsetHeight !== 0) && node.style.display.toLowerCase() !== 'none';
	},

    /**
     * Remove extraneous break tags from a node.
     *
     * @param Element
     * @return void
     **/
    killBreaks: function (e) {
        try {
            e.innerHTML = e.innerHTML.replace(readability.regexps.killBreaksRe,'<br />');       
        }
        catch (eBreaks) {
            dbg("KillBreaks failed - this is an IE bug. Ignoring.: " + eBreaks);
        }
    },

    /**
     * Clean a node of all elements of type "tag".
     * (Unless it's a youtube/vimeo video. People love movies.)
     *
     * @param Element
     * @param string tag to clean
     * @return void
     **/
    clean: function (e, tag) {
        var targetList = e.getElementsByTagName( tag );
        var isEmbed    = (tag == 'object' || tag == 'embed');
        
        for (var y=targetList.length-1; y >= 0; y--) {
            /* Allow youtube and vimeo videos through as people usually want to see those. */
            if(isEmbed) {
                var attributeValues = "";
                for (var i=0, il=targetList[y].attributes.length; i < il; i++) {
                    attributeValues += targetList[y].attributes[i].value + '|';
                }
                
                /* First, check the elements attributes to see if any of them contain youtube or vimeo */
                if (attributeValues.search(readability.regexps.videoRe) !== -1) {
                    continue;
                }

                /* Then check the elements inside this element for the same. */
                if (targetList[y].innerHTML.search(readability.regexps.videoRe) !== -1) {
                    continue;
                }
                
            }

            targetList[y].parentNode.removeChild(targetList[y]);
        }
    },
    
    /**
     * Clean an element of all tags of type "tag" if they look fishy.
     * "Fishy" is an algorithm based on content length, classnames, link density, number of images & embeds, etc.
     *
     * @return void
     **/
    cleanConditionally: function (e, tag) {

		if(!readability.flagIsActive(readability.FLAG_CLEAN_CONDITIONALLY)) {
			return;
		}

        var tagsList      = e.getElementsByTagName(tag);
        var curTagsLength = tagsList.length;

        /**
         * Gather counts for other typical elements embedded within.
         * Traverse backwards so we can remove nodes at the same time without effecting the traversal.
         *
         * TODO: Consider taking into account original contentScore here.
        **/
        for (var i=curTagsLength-1; i >= 0; i--) {
            var weight = readability.getClassWeight(tagsList[i]);
            var contentScore = (typeof tagsList[i].readability != 'undefined') ? tagsList[i].readability.contentScore : 0;
            
            dbg("Cleaning Conditionally " + tagsList[i] + " (" + tagsList[i].className + ":" + tagsList[i].id + ")" + ((typeof tagsList[i].readability != 'undefined') ? (" with score " + tagsList[i].readability.contentScore) : ''));

            if(weight+contentScore < 0)
            {
                tagsList[i].parentNode.removeChild(tagsList[i]);
            }
            else if ( readability.getCharCount(tagsList[i],',') < 10) {
                /**
                 * If there are not very many commas, and the number of
                 * non-paragraph elements is more than paragraphs or other ominous signs, remove the element.
                **/
                var p      = tagsList[i].getElementsByTagName("p").length;
                var img    = tagsList[i].getElementsByTagName("img").length;
                var li     = tagsList[i].getElementsByTagName("li").length-100;
                var input  = tagsList[i].getElementsByTagName("input").length;

                var embedCount = 0;
                var embeds     = tagsList[i].getElementsByTagName("embed");
                for(var ei=0,il=embeds.length; ei < il; ei++) {
                    if (embeds[ei].src.search(readability.regexps.videoRe) == -1) {
                      embedCount++; 
                    }
                }

                var linkDensity   = readability.getLinkDensity(tagsList[i]);
                var contentLength = readability.getInnerText(tagsList[i]).length;
                var toRemove      = false;

                if ( img > p ) {
                    toRemove = true;
                } else if(li > p && tag != "ul" && tag != "ol") {
                    toRemove = true;
                } else if( input > Math.floor(p/3) ) {
                    toRemove = true; 
                } else if(contentLength < 25 && (img === 0 || img > 2) ) {
                    toRemove = true;
                } else if(weight < 25 && linkDensity > 0.2) {
                    toRemove = true;
                } else if(weight >= 25 && linkDensity > 0.5) {
                    toRemove = true;
                } else if((embedCount == 1 && contentLength < 75) || embedCount > 1) {
                    toRemove = true;
                }

                if(toRemove) {
                    tagsList[i].parentNode.removeChild(tagsList[i]);
                }
            }
        }
    },

    /**
     * Clean out spurious headers from an Element. Checks things like classnames and link density.
     *
     * @param Element
     * @return void
    **/
    cleanHeaders: function (e) {
        for (var headerIndex = 1; headerIndex < 3; headerIndex++) {
            var headers = e.getElementsByTagName('h' + headerIndex);
            for (var i=headers.length-1; i >=0; i--) {
                if (readability.getClassWeight(headers[i]) < 0 || readability.getLinkDensity(headers[i]) > 0.33) {
                    headers[i].parentNode.removeChild(headers[i]);
                }
            }
        }
    },
	
	htmlspecialchars: function (s) {
		if (typeof(s) == "string") {
			s = s.replace(/&/g, "&amp;");
			s = s.replace(/"/g, "&quot;");
			s = s.replace(/'/g, "&#039;");
			s = s.replace(/</g, "&lt;");
			s = s.replace(/>/g, "&gt;");
		}
	
		return s;
	},

    flagIsActive: function(flag) {
        return (readability.flags & flag) > 0;
    },
    
    addFlag: function(flag) {
        readability.flags = readability.flags | flag;
    },
    
    removeFlag: function(flag) {
        readability.flags = readability.flags & ~flag;
    }

};

dotEPUB.init();