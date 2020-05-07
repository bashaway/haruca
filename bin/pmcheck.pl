#!/usr/bin/perl

use DBI;
use Encode;
use haruca;
use Net::SMTP;
use Net::SSH::Expect;
use Net::Telnet;
use strict;
use threads;
use Thread::Semaphore;
use threads::shared;



print haruca::pmcheck();

exit;

