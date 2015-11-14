__author__ = 'Jeff Ouellette'

from sqlite3 import *
from os import remove, popen

class volunteerdb:

    database = ""
    def __init__(self):
