# Testing your survey using Python Selenium and Unittest

You may wish to automate some testing to make sure that data is being saved (or nonsense values are being ignored). If you are familiar with simple Python then you can write tests using Selenium and Unittest.

Although it's possible to write tests in JavaScript or PHP, Selenium allows you to simulate browser use, typing into text boxes and clicking buttons as a participant would. If you are testing on your development setup you can also use the same Python script to connect to the SQLite database and check that the data has been saved.

An additional advantage of using Python is that the test code will be 'separate' from your survey code. Because the code must be written separately and no copy/pasting will be possible, the Python code shouldn't duplicate any bugs that may be present in the PHP code. 

## Getting started

- Install Python for Windows (using whatever system you wish).
- Open a command prompt, cd to the Python directory and install the components:
  - `python -m pip install selenium`
  - The *unittest* and *sqlite* modules should be included already

To check everything has been set up create the script *basic-setup.py* containing the following:

```python
from selenium import webdriver
from selenium.webdriver.common.by import By

driver = webdriver.Chrome()

driver.get("https://www.selenium.dev/selenium/web/web-form.html")

title = driver.title

driver.implicitly_wait(0.5)

text_box = driver.find_element(by=By.NAME, value="my-text")
submit_button = driver.find_element(by=By.CSS_SELECTOR, value="button")

text_box.send_keys("Selenium")
submit_button.click()

message = driver.find_element(by=By.ID, value="message")
text = message.text

driver.quit()
```

When you run this script it should open the Chrome browser on your desktop, browse to a special Selenium testing page and fill in parts of a form. When it is finished it should close the browser.

Don't have Chrome? Try `driver = webdriver.Firefox()` instead.

The [Selenium documentation](https://www.selenium.dev/documentation/) is thorough and easy to use. It covers all of the normal form operations and contains a few guides about testing and how best to write tests that are meaningful.

## Testing using concurrent connections

It is possible to simulate a group of participants completing a survey (multiple times if necessary). This is achieved using Python's *Queue* and *Thread* objects. There are two queues: a worker queue which simulates a group of participants and a job queue which in this case represents a list of surveys that must be completed. (The list will contain the same survey listed multiple times.)
