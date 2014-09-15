var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug',
    pageSettings: {
        userAgent: 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)'
    }
});
var url = 'http://www.aymoo.com/a.php';
var title;
var target;
var filter = "#link1";

casper.start(url);
casper.then(function(){
    target = casper.evaluate(function(){
        document.querySelector('#link1').setAttribute('target', '_self');
        return document.querySelector('#link1').getAttribute('target');
    });
    console.log(target);
    this.click('#link1');
});

casper.then(function(){
    title = casper.evaluate(function(){
        return document.title
    }); 
    console.log(title); 
    console.log(this.getCurrentUrl()); 
});
//casper.then(function(){
    
//}):
//casper.thenEvaluate(function(){
//    document.querySelector('#link1').click();
//    //this.click('#link1');
//});

//casper.thenEvaluate(function(){
//    console.log(document.querySelector('#link2').getAttribute('href'));
//    casper.exit();
//});

casper.run();
