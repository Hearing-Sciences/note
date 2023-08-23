# Handling HTTP GET variables

Previous versions of NOTE parsed the URL in the format *{server}/{action}/{module}* where *{action}* was optional and could be *preview* or *data*. This system has been revised to accommodate HTTP GET variables.

To make use of these variables:

- Direct users to https://surveys.hearing.nottingham.ac.uk/{module}?VAR1=x&VAR2=y
  - This is a normal URL containing a website and parameters.
  - I've used *VAR1* and *VAR2* but you can call the variables anything that would format correctly in a URL (alphanumerics and the underscore).
- On the **first page** of your survey call the function *process_get()* with an array of variables to search for. Declare a variable to receive the results.
  - So following the example above the command would be:
    - ```$gets = process_get(['VAR1', 'VAR2']);```
  - You don't have to use *$gets* - call the return variable whatever you want.
- If the URL contained the VAR1 and VAR2 parameters these would be captured and returned in the array.
- Even though you have the variables, they won't be automatically saved. Make sure they are recorded in form fields (presumably hidden fields) so that they are saved when the participant clicks *Next* (or save them straight away with some hacking).

The function does attempt some sanitisation but the values returned should **not** be treated as safe; they could have been hand-edited by a malicious user and may break things. As always, trust nothing and check everything.
