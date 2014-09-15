var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)'
    }
});

var title;
var target;
var res;

var search_url = 'http://s.taobao.com/search?loc=%B1%B1%BE%A9&initiative_id=tbindexz_20140913&tab=all&q=bra&filter=reserve_price%5B40%2C200%5D';
var search_selector = ".item[nid='20626276623'] h3 a";
var next_selector = ".page-next";
var sleep_time = 10;

var search_times = 0;

casper.start(search_url);

search(0);
function search(flag) {
    if (flag) {
        casper.wait(1000, function(){
            this.click(next_selector);
        });
        console.log(search_times);
    }
    casper.then(function(){
        if (this.exists(search_selector)) {
            res = casper.evaluate(function(f){
                document.querySelector(f).setAttribute('target', '_self');
                var arr = new Array();
                arr[0] = document.querySelector(f).getAttribute('target');
                arr[1] = document.querySelector(f).getAttribute('href');
                //return document.querySelector(f).getAttribute('target');
                return arr;
            }, search_selector);
            console.log(res[0]);
            console.log(res[1]);
            this.wait(1000, function(){
                this.click(search_selector);
            });
        }
        else {
            if (search_times >= 10) {
                casper.exit();
            }
            else {
                search_times++;
                search(1);
            }
        }
    });
}
casper.then(function(){
    title = casper.evaluate(function(){
        return document.querySelector(".form").innerHTML;
    });
    console.log(title);
    console.log(this.getCurrentUrl()); 
});

casper.then(function(){
    casper.exit();
});
casper.run();
