# Usernames and passwords

## TL;DR

- Create some usernames and passwords
- Store them in the database
- Pass information to the participants
- Recording usernames and passwords
- Checking against the database

## Create some usernames and passwords

How you do this is entirely up to you! You will probably want them to be easy to type to minimise errors. If you plan to randomly generate strings of characters you might also want to consider not generating vowels to avoid creating dodgy words.

'Passwords' may not have to be secure; it's possible to generate a small second username that can be used to validate the first. It makes little difference to the database or the pages you will need to create.

## Store them in the database

I'd recommend creating a table called *usernames* or *participants* containing at least the following fields:

- *ID* (always create an id!) as integer and auto-increment
- *username* as a string
- *password* also as a string
  - If you want to store secure passwords, store a hash of the password here rather than the password itself. Hashing is beyond the scope of this document so talk to Andrew if you'd like further information.
  - Very briefly, you can create a hash in most programming languages using the MD5 or SHA algorithms. MD5 is considered weak these days.

Then populate the table with your data.

## Pass information to the participants

Send the username and password information to participants. If you are using hashed passwords don't forget to send the *unhashed* password.

## Recording usernames and passwords

Your page in NOTE will require regular form fields for the username and password. The `<form type="password">` tag will generate a password friendly box that fills with discs or asterisks rather than the actual password (just like the password box when you log into Windows).

## Checking against the database

The file `db.php` can have a section for the page just like any other. You can then search for the username in the database and see if (a) a username is returned and (b) if the password matches the one supplied by the participant.

## Session variables

If you can, refer to the username/participant ID in later pages as it will be simpler to store and refer to. Use the username if necessary but don't use the password.
