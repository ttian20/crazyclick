var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)',
        loadImages: false
    }
});
//var kwd = casper.cli.get(0);
var url = casper.cli.get(0);
var nid = casper.cli.get(1);
//var url = 'http://s.taobao.com/search?&initiative_id=tbindexz_20140812&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q=' + kwd;
var title;
var target;
var filter = ".item[nid='"+nid+"'] h3 a";

casper.start(url);
casper.then(function(){
    target = casper.evaluate(function(f){
        //document.querySelector(".item[nid='36962206480'] h3 a").setAttribute('target', '_self');
        //return document.querySelector(".item[nid='36962206480'] h3 a").getAttribute('target');
        document.querySelector(f).setAttribute('target', '_self');
        return document.querySelector(f).getAttribute('target');
    }, filter);
    console.log(target);
    console.log(this.getCurrentUrl());
    this.click(filter);
});

casper.then(function(){
    title = casper.evaluate(function(){
        return document.title;
    });
    console.log(title);
    console.log(this.getCurrentUrl()); 
    this.wait(1000, function(){
        //require('utils').dump(this.getElementInfo('a:eq(1)'));
        //this.click('a[href*="category"]');
        this.click('a');
    });

//    this.wait(1000, function(){
//        this.click('.skin-box-bd .photo a');
//    });

/*    this.wait(1000, function(){
        this.click('.skin-box-bd .photo a');
    });*/
});

/*
casper.then(function(){
    if (this.exists('.item-name')) {
        require('utils').dump(this.getElementInfo('.item-name:eq(1)'));
    }
});
casper.then(function(){
    console.log(this.getCurrentUrl()); 
});

casper.then(function(){
    console.log(this.getCurrentUrl()); 
});
*/

/*
casper.wait(1000, function(){
    if (this.exists('.item-name')) {
        require('utils').dump(this.getElementInfo('.item-name:eq(1)'));
    }
});*/
//casper.then(function(){
//
//});

casper.run();
