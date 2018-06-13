#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Date    : 2016-02-28 20:17:55
# @desc    : 获取B站视频地址并保存到数据库中
# @Author  : zxr (strive965432@gmail.com)
# 
from selenium import webdriver  
from selenium.common.exceptions import NoSuchElementException  
import time  
import re
import subprocess
import requests  
import sys,os
import sys
if len(sys.argv) < 3:
   print('argv number Incorrect ') 
   sys.exit(0) 

url   = sys.argv[1]
total = int(sys.argv[2])
  
def grab(): 
    browser = webdriver.PhantomJS()
    #browser.get("https://www.bilibili.com/ranking?spm_id_from=333.334.banner_link.1#!/bangumi/167/0/3/") # Load page  
    browser.get(url) # Load page
    number = 0  
    result=[]  
    try: 
        boardItems = browser.find_elements_by_class_name('rank-item')
        countItem = len(boardItems)
        boardList = browser.find_elements_by_class_name('rank-list')
        countList = len(boardList)   
        
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
        browser.close()  
        return result  
    except NoSuchElementException:  
        assert 0, "can't find element"  

print(grab())

