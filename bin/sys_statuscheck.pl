#!/usr/bin/perl

use DBI;
use haruca;
use strict;

my ($dbh, $sth, $sql, $date, $id, $cmd, $buf,$file,$dir);
my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst);
my $lastupdate_period;
my @daily_checks;

$dbh = haruca::connect_db();

## 過去ログ整備
$buf = "";
$buf .= "<script type=\"text/javascript\"><!--\n";
$buf .= "function collapse(index) {\n";
$buf .= "  var objID=document.getElementById(index);\n";
$buf .= "  if(objID.style.display=='block') {\n";
$buf .= "    objID.style.display='none';\n";
$buf .= "  }else{\n";
$buf .= "    objID.style.display='block';\n";
$buf .= "  }\n";
$buf .= "}\n";
$buf .= "//--></script>\n";

$sql = "select value from plugin_haruca_settings where item = 'lastupdate_period'";
$lastupdate_period = $dbh->selectrow_array($sql);

if($lastupdate_period !~ /\d+/){
  $lastupdate_period = 7;
}

$buf .= print_daily_check("LastUpdate",$lastupdate_period);

#chdir("${main::datpath}");

foreach(sort glob "${main::datpath}sys_*"){
  $file = (split(/\//,$_))[-1];
  if(-f $_ ){
    if($file =~ /sys_/){
      if($file !~ /LastUpdate/){
        $dir = $file;
        $dir =~ s/^sys_//;
        $buf .= print_daily_check($dir,0);
      }
    }
  }
}

$dbh->disconnect;

print $buf;

exit;

sub print_daily_check{
  my $name = shift;
  my $days = shift;
  my (@files,@dates,%tmpfiles,$file,$hostname,$prt_date,@str_buf,$readfile,$time);

  my $buf = "";

  #ファイルリスト取得
  @files = glob "{$main::datpath}$name/*";
  sort(@files);

  # LastUpdate Print Routine
  if($name eq "LastUpdate"){
    $buf .= "<h3>$name (only latest ${days} days.)</H3>\n";

    foreach(@files){
      $date = $_;
      $date =~ s/(.+)-(\d{4}(-\d{2}){5})$/$2/;
      $tmpfiles{$date} = $_;
    }

    undef(@files);
    foreach ( sort keys(%tmpfiles)){
      push(@files,$tmpfiles{$_});
    }

  }else{
    $buf .= "<h3>$name</H3>\n";
    sort(@files);
  }


  if(@files){
    $buf .= "&nbsp;Click to Expand.<BR>\n";
    foreach $file (@files){
      $file = (split(/\//,$file))[-1];
      $hostname = $file;
      $hostname =~ s/-\d{4}(-\d{2}){5}//;
      $date = $file;
      $date=~ s/.*-(\d{4}(-\d{2}){5})/$1/;

      if($days != ""){
        @dates = split(/-/,$date);
        $prt_date = sprintf("%04d-%02d-%02d %02d:%02d:%02d",$dates[0],$dates[1],$dates[2],$dates[3],$dates[4],$dates[5]);
        $time = `date -d '$prt_date' +'%s'`;
        if((time()-$time) > ($days * 24 * 60 * 60)){next;}
        $buf .= "  &nbsp;&nbsp;&nbsp;$prt_date&nbsp;<a href=\"javascript:void(0);\" onclick=\"collapse('${hostname}_$name');\">$hostname</a><BR>\n";
      }else{
        $buf .= "  &nbsp;&nbsp;&nbsp;<a href=\"javascript:void(0);\" onclick=\"collapse('${hostname}_$name');\">$hostname</a><BR>\n";
      }

      $buf .= "  <div id=\"${hostname}_$name\" style=\"display:none\">\n";
      $buf .= "    <ul>\n";

      $buf .= "    <PRE>\n";

      $readfile = "${main::datpath}$name/$file";
      #print "READFILE : $readfile\n";
      open(FILE, "$readfile");
      @str_buf = <FILE>;
      close(FILE);

      foreach(@str_buf){
        if($_ =~ /\>(.+)\n/){
          $_ =~ s/\>(.+)\n/<font color=blue> > $1 <\/font>\n/;
        }elsif($_ =~ /\<(.+)\n/){
          $_ =~ s/\<(.+)\n/<font color=red > < $1 <\/font>\n/;
        }
        $_ =~ s/\n/<BR>\n/;
        chomp($_);
        $buf .= "$_";
      }

      $buf .= "    </PRE>\n";

      $buf .= "    </ul>\n";
      $buf .= "  </div>\n";

    }

  }else{
    $buf .= "&nbsp;No matches.<BR><BR>\n";
  }

  $buf .= "<BR>======================<BR>\n";

  return $buf;

}
