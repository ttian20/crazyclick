//var browser = require('casper').create({
//    verbose: true,
//    logLevel: "debug"
//});
var browser = require('casper').create();
var url = 'http://s.taobao.com/search?q=%E8%B4%9F%E9%87%8D%E8%83%8C%E5%BF%83&commend=all';
var title;

browser.start();
browser.thenOpen(url);

browser.then(function(){
    title = browser.evaluate(function(){
        //return document.querySelector('#link1').innerHTML;
        return document.querySelector(".item[nid='37060732386'] h3 a").getAttribute('href');
    });
});

browser.then(function(){
    console.log(title);
    browser.exit();
});

browser.run();
