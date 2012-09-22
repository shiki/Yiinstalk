
# Yeanstalk

A Yii-framework extension that wraps [Pheanstalk](https://github.com/pda/pheanstalk), 
a PHP client for [beanstalkd](http://xph.us/software/beanstalkd/). 

For now, the Pheanstalk library is packaged along with this extension. This will try to load that library unless
there is already a loaded `\Pheanstalk` class.
