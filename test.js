var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)'
    }
});
var url = 'http://s.taobao.com/search?&initiative_id=tbindexz_20140812&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q=%E8%B4%9F%E9%87%8D%E7%BB%91%E8%85%BF&suggest=0_2&_input_charset=utf-8&wq=%E8%B4%9F%E9%87%8D&suggest_query=%E8%B4%9F%E9%87%8D&source=suggest';
var title;
var target;
var filter = ".item[nid='36962206480'] h3 a";

casper.start(url);

//var function findItem(searchUrl) {
    //casper.then(
//}
casper.thenOpen(url, function(){
    target = casper.evaluate(function(f){
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
});

casper.run();
