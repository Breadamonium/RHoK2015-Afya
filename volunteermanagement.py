__author__ = 'Jeff Ouellette'

from sqlite3 import *
from os import remove, popen

dbstring = "volunteerdatabase.db"

class volunteerdb:

    dbconn = None
    open = False

    def __init__(self):
        '''
        Creates a new volunteerdb object by opening the database.
        '''
        self.open_db()
        return

    def open_db(self):
        '''
        Opens the volunteer database.
        '''
        self.dbconn = connect(dbstring)
        open = True
        return

    def close_db(self):
        '''
        Closes the volunteer database.
        '''
        self.dbconn.close()
        open = False
        return

    def new_volunteer(self, name, username, email, address, phone):
        '''
        Adds a new volunteer entry to the database.
        :param name: User's name
        :param username: User's personal username (for login purposes)
        :param email: Email address (possibly null)
        :param address: Mailing address
        :param phone: Phone number
        :return:
        '''
        if not open:
            self.open_db()

        c = self.dbconn.cursor()
        try:
            c.execute("""INSERT INTO volunteers ("name", username, address, phone, email, hours, remoteaccessallowed) VALUES (?, ?, ?, ?, ?, ?, ?)""", (name, username, address, phone, email, 0, "Yes"))


