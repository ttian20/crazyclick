var browser = require('casper').create();
var url = 'http://www.aymoo.com/a.php';
var title;

browser.start(url);
//browser.thenOpen(url);

browser.then(function(){
    title = browser.evaluate(function(){
        //return document.querySelector('#link1').innerHTML;
        return document.querySelector('#link1').getAttribute('href');
    });
});

browser.then(function(){
    console.log(title);
    browser.exit();
});

browser.run();
