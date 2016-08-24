#!/usr/bin/perl -w

###############################################################################


#use utf8;
use JSON::XS;
use Data::Dump qw(dump);

use strict;
use encoding "ru_RU.CP1251";
use Spreadsheet::WriteExcel;

	my $name= <STDIN>;

	chomp ($name);

my $import = decode_json $name;

my $workbook  = Spreadsheet::WriteExcel->new( '/var/www/social/export/data/'.$import->{1}->{name_report} );
my $worksheet1 = $workbook->add_worksheet();
my $worksheet2 = $workbook->add_worksheet();
my $worksheet3 = $workbook->add_worksheet();
my $worksheet4 = $workbook->add_worksheet();
my $worksheet5 = $workbook->add_worksheet();
my $worksheet6 = $workbook->add_worksheet();

my $wobotgreen = $workbook->set_custom_color(40, '#99CC66' );

my $bold      = $workbook->add_format( bold => 1 );
my $mbold      = $workbook->add_format( bold => 1 );
my $boldborder      = $workbook->add_format( bold => 1, border => 1 );
my $border      = $workbook->add_format( border => 1 );
my $link = $workbook->add_format();
$link->set_size(12);
$link->set_bold();
$link->set_color($wobotgreen);
$link->set_underline();
my $title = $workbook->add_format(
                                    border      => 0,
									bold		=> 1,
                                    align       => 'center',
                                    valign      => 'vcenter',
                                  );
my $tabletitle = $workbook->add_format(
                                    border      => 1,
									bg_color	=> $wobotgreen,
                                    align       => 'center',
                                    valign      => 'vcenter',
                                  );
#$tabletitle->set_text_wrap();
my $tableitem = $workbook->add_format(
                                    border      => 1,
                                  );


##############################################################################

#$worksheet1->set_column('A', 150);
$worksheet1->set_column('A:A', 40);
$worksheet1->set_column('B:B', 40);

my $text1 = [
	[ '�������� ������', '������ �� ������', '���-�� ������� � ������','���-�� ��������������� ������� � ������','���-�� ��������� ������� � ������','��������� �������� (��/���)','���������� ��������(��/���)', '������� ������ (�����������)', '������ ������������' ],
	[
	$import->{1}->{name},
	$import->{1}->{link},
	$import->{1}->{count_people},
	$import->{1}->{count_block},
	$import->{1}->{count_delete},
	$import->{1}->{mobile},
	$import->{1}->{photo},
	$import->{1}->{userpic},
	$import->{1}->{start}
	 ],
];
$worksheet1->write( 'A1', $text1 );

$worksheet1->write( 'C9', $import->{1}->{end} );

$worksheet1->insert_image('A11', '/var/www/project/excel/logo2.png', 0, 0);

my $chart11 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart11->add_series(
    categories => '=Sheet1!$A$3:$A$5',
    values     => '=Sheet1!$B$3:$B$5',
    name       => '��������� ������',
);
$chart11->set_title( name => '���������� ������� � ������' );
$worksheet1->insert_chart( 'A18', $chart11 );

##############################################################################

$worksheet2->set_column('A:A', 10);
$worksheet2->set_column('B:B', 15);
$worksheet2->set_column('C:C', 10);
$worksheet2->set_column('D:D', 17);
$worksheet2->set_column('E:E', 10);
$worksheet2->set_column('F:F', 10);
$worksheet2->set_column('G:G', 10);
$worksheet2->set_column('H:H', 50);

my $text21 = [
	[ 
	'����� ���-�� ������',
	'����� ����� ������������',
	'����� ����� ������'
    ],
	[
	$import->{2}->{count_posts},
	$import->{2}->{count_comments},
	$import->{2}->{count_likes}
	],
];
$worksheet2->write( 'A1', $text21 );

$worksheet2->write( 'A4', $import->{2}->{types} );
	
my $text22 = [
	[ '���'],
	['����'],
	['�����'],
	['Id ������'],
	['�����'],
	['���-�� ������'],
	['���-�� ������������'],
	['�����'],
];
$worksheet2->freeze_panes(20, 0);

#$worksheet2->write( 'A20', $text22, $tabletitle );
#$worksheet2->write( 'A21', $import->{2}->{posts}, $tableitem );

$worksheet6->write( 'A1', $import->{2}->{posts_graph} );

my $num21 = @{$import->{2}->{types}[0]};
my $chart21 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart21->add_series(
    categories => '=Sheet2!$A$4:$A$'.($num21+3),
    values     => '=Sheet2!$B$4:$B$'.($num21+3),
    name       => '������������� ��������� �� ����',
);
$chart21->set_title( name => '���� ���������' );
$worksheet2->insert_chart( 'C1', $chart21 );

my $chart22 = $workbook->add_chart( type => 'line', embedded => 1 );
my $num22 = @{$import->{2}->{posts_graph}[0]};
$chart22->add_series(
    categories => '=Sheet6!$A$2:$A$'.($num22+1),
    values     => '=Sheet6!$B$2:$B$'.($num22+1),
    name       => $import->{2}->{posts_graph}[1][0],
);
$chart22->add_series(
    categories => '=Sheet6!$A$2:$A$'.($num22+1),
    values     => '=Sheet6!$C$2:$C$'.($num22+1),
    name       => $import->{2}->{posts_graph}[2][0],
);
$chart22->add_series(
    categories => '=Sheet6!$A$2:$A$'.($num22+1),
    values     => '=Sheet6!$D$2:$D$'.($num22+1),
    name       => $import->{2}->{posts_graph}[3][0],
);
$chart22->add_series(
    categories => '=Sheet6!$A$2:$A$'.($num22+1),
    values     => '=Sheet6!$E$2:$E$'.($num22+1),
    name       => $import->{2}->{posts_graph}[4][0],
);
$chart22->add_series(
    categories => '=Sheet6!$A$2:$A$'.($num22+1),
    values     => '=Sheet6!$F$2:$F$'.($num22+1),
    name       => $import->{2}->{posts_graph}[5][0],
);
$chart22->set_title( name => '������ ���������� �� ���� ���������' );
$chart22->set_x_axis( name => '���' );
$chart22->set_y_axis( name => '���������� ����������' );
$worksheet2->insert_chart( 'H1', $chart22 );

##############################################################################

$worksheet3->set_column('A:A', 20);
$worksheet3->set_column('B:B', 35);
$worksheet3->set_column('C:C', 100);

my $text31 = [
	[ 
	'���-�� �������������, ��������� ���� �� 1 ��������',
	'���-�� �������������, ��������� 5 � ����� ��������',
	'���-�� ������������� �� ��������� �� ������ ��������'
    ],
	[
	$import->{3}->{less5},
	$import->{3}->{more5},
	$import->{3}->{passive}
	],
];
$worksheet3->write( 'A1', $text31 );

my $text32 = [
	[ 
	'id'
    ],
	[
	'��� ������������'
	],
];

my $text33 = [
	[ 
	'���-�� ��������'
    ],
	[
	'���������� �����'
	],
];

$worksheet3->write( 'A5', $text33 );
my $text34 = $import->{3}->{topactions};

$worksheet3->write( 'A6', $text34 );
#$worksheet4->freeze_panes(20, 0);
#$worksheet3->write( 'B20', $text32, $tabletitle );
#$worksheet3->write( 'A20', '������ �������������, ��������� 5 � ����� ��������' );

#$worksheet3->write( 'B21', $import->{3}->{list}, $tableitem );

#my $chart31 = $workbook->add_chart( type => 'pie', embedded => 1  );
#$chart31->add_series(
#    categories => '=Sheet3!$A$1:$A$3',
#    values     => '=Sheet3!$B$1:$B$3',
#    name       => '������������� ������������� �� ���������� ����������� ��������',
#);
#$chart31->set_title( name => '���������� �������������' );
#$worksheet3->insert_chart( 'C1', $chart31 );

##############################################################################

my $text41 = [
	[ '�'],
	['���'],
	['�������'],
	['�����'],
	['���'],
	['Id'],
	['���'],
	['���� ��������'],
	['�����'],
	['������'],
	['��'],
	['������ �� ������'],
	['��'],
	['������� (% � ������)'],
	['���������'],
	['��� �������'],
	['������'],
	['���������'],
	['��� ���������� �������'],
];

#['���-�� ��������'],
#['���-�� ����'],
#['���-�� �����'],
#['���-�� �����'],
#['���-�� ����� ������������'],
#['���-�� �������'],
#['���-�� �������'],
#['���-�� �����������'],
#['�� �������� ��������'],
#$worksheet4->set_row(1, 3);
$worksheet4->set_column('A:A', 20);
$worksheet4->set_column('B:B', 20);
$worksheet4->set_column('C:C', 5);
$worksheet4->set_column('D:D', 20);
$worksheet4->set_column('E:E', 20);
$worksheet4->set_column('F:F', 5);
$worksheet4->set_column('G:G', 20);
$worksheet4->set_column('H:H', 20);
$worksheet4->set_column('I:I', 12);
$worksheet4->set_column('J:J', 12);
$worksheet4->set_column('K:K', 2);
$worksheet4->set_column('L:L', 20);
$worksheet4->set_column('M:M', 2);
$worksheet4->set_column('N:N', 5);
$worksheet4->set_column('O:O', 20);
$worksheet4->set_column('P:P', 20);
$worksheet4->set_column('Q:Q', 30);
$worksheet4->set_column('R:R', 30);
$worksheet4->set_column('S:S', 5);
#$worksheet4->freeze_panes(1, 0);

#$worksheet4->write( 'A1', $text41, $tabletitle );

#$worksheet4->write( 'A2', $import->{4}, $tableitem );

my $text42 = [
	[ 
	'���-�� ������',
	'���-�� ������',
	'���-�� ������������� ��� ��������',
	'���-�� ������������� � ���������',
    ],
	[
	$import->{4}->{count_woman},
	$import->{4}->{count_man},
	$import->{4}->{count_without_photo},
	$import->{4}->{count_with_photo}
	],
];
$worksheet4->write( 'A3', $text42 );
my $text42 = [
	[ 
	'������������� ���������� �����',
    ],
	[
	],
	[
	],
	[
	'������������� �������������(�� �������)',
	],
	[
	],
	[
	],
	[
	'������������� �������������(�� �������)',
	]
	#[
	#$import->{3}->{count_woman},
	#$import->{3}->{count_man},
	#$import->{3}->{count_without_photo},
	#$import->{3}->{count_with_photo}
	#],
];
#$worksheet4->write( 'A8', $text42 );

my $text43 = [
	[ 
	$import->{4}->{age},
    ],
];
$worksheet4->write( 'A10', $text43 );

my $text44 = [
	[ 
	$import->{4}->{loc},
    ],
];
$worksheet4->write( 'D10', $text44 );

my $text45 = [
	[ 
	$import->{4}->{cou},
    ],
];
$worksheet4->write( 'G10', $text45 );

my $text46 = [
	[ 
	'�������'
    ],
	[
	'����������'
	],
	[
	],
	[
	'�����'
	],
	[
	'����������'
	],
	[
	],
	[
	'������',
	],
	[
	'����������'
	]
	#[
	#$import->{3}->{count_woman},
	#$import->{3}->{count_man},
	#$import->{3}->{count_without_photo},
	#$import->{3}->{count_with_photo}
	#],
];
$worksheet4->write( 'A9', $text46 );
##############################################################################
$worksheet5->set_column('A:A', 5);
$worksheet5->set_column('B:B', 17);
$worksheet5->set_column('C:C', 17);
$worksheet5->set_column('D:D', 17);
$worksheet5->set_column('E:E', 5);
$worksheet5->set_column('F:F', 20);
$worksheet5->set_column('G:G', 5);
$worksheet5->set_column('H:H', 10);
$worksheet5->set_column('I:I', 12);
$worksheet5->set_column('J:J', 12);
$worksheet5->set_column('K:K', 2);
$worksheet5->set_column('L:L', 20);
$worksheet5->set_column('M:M', 2);
$worksheet5->set_column('N:N', 5);
$worksheet5->set_column('O:O', 20);
$worksheet5->set_column('P:P', 20);
$worksheet5->set_column('Q:Q', 30);
$worksheet5->set_column('R:R', 30);
$worksheet5->set_column('S:S', 5);
$worksheet5->freeze_panes(1, 0);

my $text51 = [
	[ '�'],
	['���'],
	['�������'],
	['�����'],
	['���'],
	['Id'],
	['���'],
	['���� ��������'],
	['�����'],
	['������'],
	['��������� ����'],
	['������ �� ������'],
	['����������� �� ������������'],
	['������� (% � ������)'],
	['���������'],
	['��� �������'],
	['������'],
	['���������'],
	['��� ���������� �������'],
];

#['���-�� ��������'],
#['���-�� ����'],
#['���-�� �����'],
#['���-�� �����'],
#['���-�� ����� ������������'],
#['���-�� �������'],
#['���-�� �������'],
#['���-�� �����������'],
#['�� �������� ��������'],
$worksheet5->write( 'A1', $text51, $tabletitle );

$worksheet5->write( 'A2', $import->{5}->{not_in_group}, $tableitem );
$worksheet4->merge_range('A8:B8', '������������� ���������� �����', $tableitem);
$worksheet4->merge_range('D8:E8', '������������� �������������(�� �������)', $tableitem);
$worksheet4->merge_range('G8:H8', '������������� �������������(�� �������)', $tableitem);
##############################################################################
__END__
