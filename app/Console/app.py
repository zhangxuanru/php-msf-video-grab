from flask import Flask,Response
from flask import request
#from flask import jsonify
import json
import base64

from selenium import webdriver  
from selenium.common.exceptions import NoSuchElementException  
import time  
import re
import subprocess
import requests  
import sys,os
import sys

app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def home():
     #print('begin:')
     url = request.args.get('url')
     total = request.args.get('total')
     if total is None:
        total = 1
     total = int(total)
     url = base64.urlsafe_b64decode(url)
     url = bytes.decode(url)  
     '''      
     cap = webdriver.DesiredCapabilities.PHANTOMJS
     cap["phantomjs.page.settings.resourceTimeout"] = 5000
     cap["phantomjs.page.settings.loadImages"] = True
     cap["phantomjs.page.settings.disk-cache"] = True
     cap["phantomjs.page.settings.userAgent"] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0",
     cap["phantomjs.page.customHeaders.User-Agent"] = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0', 
      
     dcap = dict(webdriver.DesiredCapabilities.PHANTOMJS)
     dcap["phantomjs.page.settings.userAgent"] = (
      "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/53 "
      "(KHTML, like Gecko) Chrome/15.0.87"
     )   
     browser = webdriver.PhantomJS(desired_capabilities=dcap)
     #browser.get("https://www.bilibili.com/ranking?spm_id_from=333.334.banner_link.1#!/bangumi/167/0/3/") # Load page  
     '''
     number = 0  
     result=[]  
     #print('try:')
     dcap = dict(webdriver.DesiredCapabilities.PHANTOMJS)
     dcap["phantomjs.page.settings.userAgent"] = (
           "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/53 "
           "(KHTML, like Gecko) Chrome/15.0.87"
       )   
     browser = webdriver.PhantomJS(desired_capabilities=dcap)
     #print(browser)
     #browser.quit()
     #print("####")
     #print(url)
     #print("####")
    # return url
     #url = 'https://www.bilibili.com/ranking?spm_id_from=333.334.banner_link.1#!/all/36/0/3/'
     browser.get(url) # Load page
     #browser.get("https://www.bilibili.com/ranking?spm_id_from=333.334.banner_link.1#!/bangumi/167/0/3/") # Load page  
    # print('browser.get:')
     #browser.quit()
     #return url
     
     boardItems = browser.find_elements_by_class_name('rank-item')
     countItem = len(boardItems)
     boardList = browser.find_elements_by_class_name('rank-list')
     countList = len(boardList)   
        #browser.quit()   
     for i in range(0,countList):  
         boardLiList = boardList[i].find_elements_by_tag_name('li')
         boardLiLen  = len(boardLiList)            
         for j in range(0,boardLiLen):
             if boardLiList[j]:
                 video_sid   = boardLiList[j].get_attribute('data-season-id')
                 if video_sid is None:
                      break 
                 result.append('sid_'+video_sid)
                 number+=1;
                 if number >= total and total > 0:
                     break

     for i in range(0,countItem):  
           videoWatch = boardItems[i].find_elements_by_class_name('watch-later')
           if videoWatch:
               video_aid   = videoWatch[0].get_attribute('aid')
               if video_aid is None:
                   break
               result.append('aid_'+video_aid)
               number+=1;
               if number >= total and total > 0:
                   break   
     if browser:
         browser.quit()   
     return Response(json.dumps(result), mimetype='application/json')

if __name__ == '__main__':
    app.run(
       host = '0.0.0.0',
       port = 5000,  
   )
