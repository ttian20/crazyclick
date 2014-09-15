var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)'
    }
});

//var url = 'http://detail.tmall.com/item.htm?spm=a230r.1.14.210.8vWc5l&id=37191891128';
var url = 'http://detail.tmall.com/item.htm?spm=a1z10.4.w5003-8466280912.3.QkFnN1&id=40673657543&scene=taobao_shop';
var body;
var title;
casper.start(url,function(){
    this.scrollToBottom();
});

casper.then(function(){
/*    title = casper.evaluate(function(){
        return document.title;
    });
    console.log(title);
    console.log(this.getCurrentUrl()); */
    var body = casper.evaluate(function(){
        var aele = document.querySelectorAll('a');
        for (var i = 0, len = aele.length; i < len; i++) {
            aele[i].setAttribute('target', '_self');
        }
        return document.body.innerHTML;
    });
    //console.log(body);

    if (this.exists(".slogo-shopname")) {
        this.wait(5000, function(){
            this.click(".slogo-shopname");
        });
    }

});

casper.then(function(){
    var body = casper.evaluate(function(){
        return document.body.innerHTML;
    });
    //console.log(body);
});

casper.run();
