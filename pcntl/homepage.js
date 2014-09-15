var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)'
    }
});

var url = 'http://yiner.tmall.com/?spm=a220o.1000855.1997427721.d4918089.KBXNQG';
var body;
var title;
casper.start(url,function(){
    this.scrollToBottom();
});

casper.then(function(){
    var body = casper.evaluate(function(){
        var aele = document.querySelectorAll('a');
        for (var i = 0, len = aele.length; i < len; i++) {
            aele[i].setAttribute('target', '_self');
        }
        return document.body.innerHTML;
    });
    //console.log(body);

    /*if (this.exists(".fst-cat-name")) {
        this.wait(5000, function(){
            this.click(".fst-cat-name");
        });
    }*/

    if (this.exists("a[href*='tmall.com/p/']")) {
        this.wait(5000, function(){
            this.click("a[href*='tmall.com/p/']");
        });
    }

    if (this.exists("a[href*='tmall.com/category-']")) {
        this.wait(5000, function(){
            this.click("a[href*='tmall.com/category-']");
        });
    }

});

casper.run();
