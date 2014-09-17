var args = phantom.args;
console.log('phantom.args:',args);

var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36'
    }
});
var title;
var target;

var search_url = casper.cli.get(0);
var search_selector = casper.cli.get(1);
var next_selector = casper.cli.get(2)
var sleep_time = parseInt(casper.cli.get(3));

casper.start(search_url);

casper.then(function(){
    title = casper.evaluate(function(){
        return document.title;
    });
    casper.log(title);
    casper.exit();
});

/*
casper.start('http://www.tmall.com');
casper.then(function(){
    casper.evaluate(function(k){
        document.querySelector('form[name="searchTop"]').setAttribute('target', '_self');
        document.querySelector('input[name="q"]').setAttribute('value', k);
        document.querySelector('form[name="searchTop"]').submit();
    }, kwd);
});

search();
function search() {
    casper.then(function(){
        target = casper.evaluate(function(f){
            document.querySelector(f).setAttribute('target', '_self');
            return document.querySelector(f).getAttribute('target');
        }, search_selector);
        console.log(target);
        console.log(this.getCurrentUrl());
        if (this.exists(search_selector)) {
            this.wait(2000, function(){
                this.click(search_selector);
            });
        }
        else {
            this.wait(2000, function(){
                this.click(next_selector);
            });
            search();
        }
    });
}

casper.then(function(){
    title = casper.evaluate(function(){
        return document.title;
    });
    console.log(title);
    console.log(this.getCurrentUrl()); 
    if (this.exists("a[href*='category']")) {
        this.wait(5000, function(){
            this.click("a[href*='category']");
        });
    }
    if (this.exists("a[href^='http://detail.tmall']")) {
        this.wait(2000, function(){
            this.click("a[href^='http://detail.tmall']");
        });
    }
    casper.exit();
});
*/

casper.run();
