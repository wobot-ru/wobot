#!/usr/bin/perl -w

###############################################################################


#use utf8;
use JSON::XS;
use Data::Dump qw(dump);

use strict;
use encoding "ru_RU.CP1251";
#use Spreadsheet::WriteExcel::XLSX;
use Excel::Writer::XLSX;
	my $name= <STDIN>;

	chomp ($name);

my $import = decode_json $name;

binmode( STDOUT );
my $workbook = Excel::Writer::XLSX->new( \*STDOUT );
#my $workbook  = Spreadsheet::WriteExcel->new( '-' );
my $worksheet1 = $workbook->add_worksheet('�����');
my $worksheet2 = $workbook->add_worksheet('����������');
my $worksheet3 = $workbook->add_worksheet('����������');
my $worksheet4 = $workbook->add_worksheet('���������� ����������');
my $worksheet5 = $workbook->add_worksheet('�������');
my $worksheet6 = $workbook->add_worksheet('�������');
my $worksheet7 = $workbook->add_worksheet('������ ������');
my $worksheet8 = $workbook->add_worksheet('�����������');
my $worksheet9 = $workbook->add_worksheet('��������� ����������');
my $worksheet10 = $workbook->add_worksheet('�������������� ���������');

$worksheet1->set_column('B:B', 40);	
$worksheet1->set_column('C:C', 20);
$worksheet2->set_column('B:B', 20);
$worksheet2->set_column('C:C', 40);
if ($import->{'export_digest'})
{
	$worksheet3->set_column('A:C', 15);
	$worksheet3->set_column('D:D', 30);
	$worksheet3->set_column('E:E', 20);
	$worksheet3->set_column('F:G', 40);
	$worksheet3->set_column('H:J', 5);
	$worksheet3->set_column('K:K', 10);
	$worksheet3->set_column('L:L', 20);
	$worksheet3->set_column('M:M', 5);
	$worksheet3->set_column('N:Q', 20);
}
else
{
	$worksheet3->set_column('A:C', 15);
	$worksheet3->set_column('D:D', 30);
	$worksheet3->set_column('E:E', 20);
	$worksheet3->set_column('F:F', 40);
	$worksheet3->set_column('G:I', 5);
	$worksheet3->set_column('J:J', 10);
	$worksheet3->set_column('K:K', 20);
	$worksheet3->set_column('L:L', 5);
	$worksheet3->set_column('M:P', 20);
}
#$worksheet3->set_column('N:O', 20);
#$worksheet4->set_column('A:A', 5);
$worksheet4->set_column('B:B', 15);
$worksheet4->set_column('C:C', 15);
$worksheet4->set_column('D:D', 40);
$worksheet4->set_column('E:E', 40);
$worksheet4->set_column('F:F', 40);
$worksheet4->set_column('G:H', 10);
$worksheet5->set_column('B:B', 20);
$worksheet5->set_column('F:J', 20);
#$worksheet6->set_column('A:A', 5);
$worksheet6->set_column('B:B', 20);
$worksheet6->set_column('C:C', 30);
$worksheet6->set_column('D:H', 12);
$worksheet6->set_column('I:I', 20);
#$worksheet7->set_column('A:A', 5);
$worksheet7->set_column('B:B', 20);
$worksheet7->set_column('C:C', 30);
$worksheet7->set_column('D:H', 12);
$worksheet7->set_column('I:I', 20);
#$worksheet8->set_column('A:A', 5);
$worksheet8->set_column('A:C', 20);
$worksheet8->set_column('D:D', 30);
$worksheet8->set_column('E:E', 15);
$worksheet8->set_column('F:F', 40);
$worksheet8->set_column('G:G', 15);
$worksheet8->set_column('H:H', 20);
$worksheet8->set_column('I:I', 5);
$worksheet8->set_column('J:J', 10);
$worksheet8->set_column('K:L', 20);
$worksheet9->set_column('B:B', 20);
$worksheet9->set_column('C:F', 15);
$worksheet10->set_column('B:F', 15);
$worksheet10->set_column('H:H', 25);
$worksheet10->set_column('I:I', 15);


my $bold      = $workbook->add_format( bold => 1 );
my $mbold      = $workbook->add_format( bold => 1 );
my $boldborder      = $workbook->add_format( bold => 1, border => 1 );
my $border      = $workbook->add_format( border => 1 );
my $link = $workbook->add_format();
$link->set_size(12);
$link->set_bold();
$link->set_color('green');
$link->set_underline();
my $link2 = $workbook->add_format();
$link2->set_size(12);
$link2->set_color('blue');
$link2->set_underline();

my $title = $workbook->add_format(
                                    border      => 0,
									bold		=> 1,
                                    align       => 'center',
                                    valign      => 'vcenter',
                                  );




#�����

	
	$worksheet1->merge_range('A6:H6', '����� �� �����������.', $title);
	
	
	my $text1 = [
		[ '����:', '������:', '��������� ������:' ],
		[ $import->{1}->{order_name}, $import->{1}->{order_time}, $import->{1}->{order_keyword} ],
	];
	$worksheet1->write( 'B1', '�������� �����:' );
	$worksheet1->write( 'B2', 'E-mail' );
	$worksheet1->write( 'B3', '�������' );
	$worksheet1->write( 'C2', 'mail@wobot.ru' );
	$worksheet1->write( 'C3', '+7 (495) 669-27-33, +7 (968) 531-79-73' );
	
	$worksheet1->write( 'B7', $text1 );

	$worksheet1->merge_range('A11:H11', '���������� ������:', $title);

	$worksheet1->write('B12', 'internal:�����!A1','�����',$link);
	$worksheet1->write('B13', 'internal:����������!A1','����������',$link);
	$worksheet1->write('B14', 'internal:����������!A1','����������',$link);
	$worksheet1->write('B15', 'internal:\'���������� ����������\'!A1',q{���������� ����������},$link);
	$worksheet1->write('B16', 'internal:�������!A1','�������',$link);
	$worksheet1->write('B17', 'internal:�������!A1','�������',$link);
	$worksheet1->write('B18', 'internal:\'������ ������\'!A1',q{������ ������},$link);
	$worksheet1->write('B19', 'internal:�����������!A1','�����������',$link);
	$worksheet1->write('B20', 'internal:\'��������� ����������\'!A1',q{��������� ����������},$link);
	$worksheet1->write('B21', 'internal:\'�������������� ���������\'!A1',q{�������������� ���������},$link);

##############################################################################
#����������

my $table2 = [
    [ '���-�� �����������:', '���������� �������:', '� ������� ������/�����:', '���������� ��������:', '�����:'],#'�������������:'],
    [ $import->{2}->{count_posts}, $import->{2}->{uniq_auth}, $import->{2}->{post_in_day}, $import->{2}->{count_hosts}, $import->{2}->{audience}],# $import->{2}->{engage} ],
];

$worksheet2->write( 'B4', $table2, $bold );
#$worksheet10->write( 'H4', ['������������� ��������� �� �����'], $boldborder );
$worksheet2->write( 'B9', $import->{10}->{eng_mas}, $bold );

my $headings2 = [ '����', '���-�� ����������', '����' ];

$worksheet2->write( 'B32', ['���������� �� ����'], $bold );
$worksheet2->write( 'B33', $headings2, $boldborder );
$worksheet2->write( 'B34', $import->{2}->{graph}, $border );

my $chart2 = $workbook->add_chart( type => 'line', embedded => 1 );
$chart2->set_style( 10 );
my $num = @{$import->{2}->{graph}[0]};
$chart2->add_series(
    categories => '=\'����������\'!$B$34:$B$'.($num+33),
    values     => '=\'����������\'!$C$34:$C$'.($num+33),
    name       => $import->{1}->{order_name},
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);

# Add some labels.
$chart2->set_title( name => '������ ����������' );
$chart2->set_x_axis( name => '���' );
$chart2->set_y_axis( name => '���������� ����������' );

# Insert the chart into the main worksheet.
$worksheet2->insert_chart( 'B13', $chart2 );

##############################################################################
#����������

my $headings3 = [];
if ($import->{'export_digest'}) 
{
	$headings3 = [ '����', '�����', '������', '������', '��� �������', '������ �����', '��������', '���.', '����.', '����.', '�������������', '���', '���', '����.', '�����', '������', '����' ];
}
else 
{
	$headings3 = [ '����', '�����', '������', '������', '��� �������', '������ �����', '���.', '����.', '����.', '�������������', '���', '���', '����.', '�����', '������', '����' ];
}

$worksheet3->write( 'A4', ['����������'], $bold );
$worksheet3->write( 'A5', $headings3, $bold );
if ($import->{'export_digest'}) 
{
	$worksheet3->write( 'A6', [$import->{3}[0]] );
	$worksheet3->write( 'B6', [$import->{3}[1]] );
	$worksheet3->write( 'C6', [$import->{3}[2]] );
	$worksheet3->write( 'D6', [$import->{3}[3]] );
	$worksheet3->write( 'E6', [$import->{3}[4]] );
	$worksheet3->write( 'F6', [$import->{3}[5]] );
	# $worksheet3->write( 'G6', [$import->{3}[6]] );
	$worksheet3->write( 'H6', [$import->{3}[7]] );
	$worksheet3->write( 'I6', [$import->{3}[8]] );
	$worksheet3->write( 'J6', [$import->{3}[9]] );
	$worksheet3->write( 'K6', [$import->{3}[10]] );
	$worksheet3->write( 'L6', [$import->{3}[11]] );
	$worksheet3->write( 'M6', [$import->{3}[12]] );
	$worksheet3->write( 'N6', [$import->{3}[13]] );
	$worksheet3->write( 'O6', [$import->{3}[14]] );
	$worksheet3->write( 'P6', [$import->{3}[15]] );
	$worksheet3->write( 'Q6', [$import->{3}[16]] );
	# $result = [ $result ]
	my $iter=0;
	# $url_info = [ $import->{3}[5] ];
	foreach my $name (@{$import->{3}[6]}) {
	 	# $worksheet3->write( 'A'.($iter+6), $name );
	 	$worksheet3->write_url( 'G'.($iter+6), $import->{3}[3][$iter], $name , $link2);
	 	$iter++;
	}
}
else
{
	$worksheet3->write( 'A6', $import->{3} );
}


###############################################################################
#���������� �����

#my $headings41 = [ '�����','���-�� ����������'];
#my $data41 = [
#	['�� �������','�������','������','�����'],
#	['10','5','3','2'],
#];
#$worksheet4->write( 'A5', $headings41, $boldborder );
#$worksheet4->write( 'A6', $data41, $border );

$worksheet4->merge_range('A5:G5', '���������� ����� (���-10)', $mbold);

my $headings42 = [ '�', '����', '�����', '�����', '������','������ �� ������', '�������������', '�����' ];

$worksheet4->write( 'A6', $headings42, $boldborder );
$worksheet4->write( 'A7', $import->{4}, $border );

###############################################################################
#�������

$worksheet5->write( 'B3', ['������������� ���������� �� �������� � ���������'], $bold );
$worksheet5->write( 'B4', $import->{5}->{proc}, $border );

my $chart51 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart51->set_style( 10 );
$chart51->add_series(
    categories => '=\'�������\'!$B$4:$B$'.(3+@{$import->{5}->{proc}[0]}),
    values     => '=\'�������\'!$C$4:$C$'.(3+@{$import->{5}->{proc}[0]}),
    name       => '������������� ��������� �� ����������',
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart51->set_title( name => '���������' );
$worksheet5->insert_chart( 'E4', $chart51 );

my $headings52 = [ '������', '���-��' ];
$worksheet5->write( 'B23', ['������������� ���������� �� ��������'], $bold );
$worksheet5->write( 'B24', $headings52, $boldborder );
$worksheet5->write( 'B25', $import->{5}->{count}, $border );

#$worksheet5->write( 'E24', $headings53, $boldborder );
$worksheet5->write( 'E41', ['������������� ���������� �� ����(���-5 ��������)'], $bold );
$worksheet5->write( 'E42', $import->{5}->{top_count}, $border );

my $chart52 = $workbook->add_chart( type => 'line', embedded => 1 );
$chart52->set_style( 10 );
# Add some labels.
$chart52->set_title( name => '�������� ���������� ��������' );
$chart52->set_x_axis( name => '���' );
$chart52->set_y_axis( name => '���������� ����������' );
my $num52 = @{$import->{5}->{top_count}[0]};
$chart52->add_series(
    categories => '=\'�������\'!$E$43:$E$'.($num52+41),
    values     => '=\'�������\'!$F$43:$F$'.($num52+41),
    name       => $import->{5}->{top_count}[1][0],
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart52->add_series(
    categories => '=\'�������\'!$E$43:$E$'.($num52+41),
    values     => '=\'�������\'!$G$43:$G$'.($num52+41),
    name       => $import->{5}->{top_count}[2][0],
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart52->add_series(
    categories => '=\'�������\'!$E$43:$E$'.($num52+41),
    values     => '=\'�������\'!$H$43:$H$'.($num52+41),
    name       => $import->{5}->{top_count}[3][0],
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart52->add_series(
    categories => '=\'�������\'!$E$43:$E$'.($num52+41),
    values     => '=\'�������\'!$I$43:$I$'.($num52+41),
    name       => $import->{5}->{top_count}[4][0],
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart52->add_series(
    categories => '=\'�������\'!$E$43:$E$'.($num52+41),
    values     => '=\'�������\'!$J$43:$J$'.($num52+41),
    name       => $import->{5}->{top_count}[5][0],
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);

# Insert the chart into the main worksheet.
$worksheet5->insert_chart( 'E24', $chart52 );

###############################################################################
#�������

my $headings61 = [ '�', '������', '������ �� �������', '�����', '���������', '�������','�������','������������', '������' ];
#$worksheet6->write( 'B3', ['�������'], $bold );
$worksheet6->write( 'A4', $headings61, $boldborder );
$worksheet6->write( 'A5', $import->{6}, $border );

###############################################################################
#������ ������

my $headings71 = [ '�', '����� ������', '������ �� �������', '����� ���������', '�����', '����������', '����������', '������������', '������' ];
#$worksheet7->write( 'B3', ['������ ������'], $bold );
$worksheet7->write( 'A4', $headings71, $boldborder );
$worksheet7->write( 'A5', $import->{7}, $border );

###############################################################################
#�����������

my $headings81 = [ '����', '�����������', '����������', '����������',  ];
$worksheet8->write( 'A53', ['����������� ���������� �� ����'], $bold );
$worksheet8->write( 'A54', $headings81, $boldborder );
$worksheet8->write( 'A55', $import->{8}->{dinams}, $border );

my $headings82 = [ '', '���-��' ];
my $data82 = [
	['����������', '����������', '�����������'],
	['5%','10%','85%'],
];
my $data821 = [
	['�����������','����������','����������'],
];
$worksheet8->write( 'F53', ['����������� ����������'], $bold );
$worksheet8->write( 'F54', $headings82, $boldborder );
$worksheet8->write( 'F55', $data821, $boldborder );
$worksheet8->write( 'G55', $import->{8}->{all}, $border );

$worksheet8->write( 'A25', '��� 10 ����������(�� ������)', $bold );
my $headings83 = [ '����', '�����' , '������', '������', '��� �������', '������ �����', '�������������', '���', '���', '�����', '������', '����'];
$worksheet8->write( 'A26', $headings83, $boldborder );
$worksheet8->write( 'A27', $import->{8}->{top_positive}, $border );

$worksheet8->write( 'A40', '��� 10 ����������(�� ������)', $bold );
my $headings84 = [ '����', '�����' , '������', '������', '��� �������', '������ �����', '�������������', '���', '���', '�����', '������', '����'];
$worksheet8->write( 'A41', $headings84, $boldborder );
$worksheet8->write( 'A42', $import->{8}->{top_negative}, $border );


my $chart81 = $workbook->add_chart( type => 'line', embedded => 1 );
$chart81->set_style( 10 );
my $num81 = @{$import->{8}->{dinams}[0]};
$chart81->add_series(
    categories => '=\'�����������\'!$A$55:$A$'.($num81+54),
    values     => '=\'�����������\'!$B$55:$B$'.($num81+54),
    name       => '�����������',
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart81->add_series(
    categories => '=\'�����������\'!$A$55:$A$'.($num81+54),
    values     => '=\'�����������\'!$C$55:$C$'.($num81+54),
    name       => '����������',
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart81->add_series(
    categories => '=\'�����������\'!$A$55:$A$'.($num81+54),
    values     => '=\'�����������\'!$D$55:$D$'.($num81+54),
    name       => '����������',
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);

# Add some labels.
$chart81->set_title( name => '������ ����������� ����������' );
$chart81->set_x_axis( name => '���' );
$chart81->set_y_axis( name => '���������� ����������' );

# Insert the chart into the main worksheet.
$worksheet8->insert_chart( 'B4', $chart81 );


my $chart82 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart82->set_style( 10 );
$chart82->add_series(
    categories => '=\'�����������\'!$F$55:$F$57',
    values     => '=\'�����������\'!$G$55:$G$57',
    name       => '����������� ����������� �������',
);
$chart82->set_title( name => '�����������' );
$worksheet8->insert_chart( 'F4', $chart82 );

#my $chart81 = $workbook->add_chart( type => 'line', embedded => 1 );
#my $num81 = @{$import->{8}->{dinams}[0]};
#$chart81->add_series(
#    categories => '=Sheet8!$A$55:$A$'.($num81+54),
#    values     => '=Sheet8!$B$55:$B$'.($num81+54),
#    name       => '����������',
#);
#$chart81->add_series(
#    categories => '=Sheet8!$A$55:$A$'.($num81+54),
#    values     => '=Sheet8!$�$55:$�$'.($num81+54),
#    name       => '����������',
#);
#$chart81->add_series(
#    categories => '=Sheet8!$A$55:$A$'.($num81+54),
#    values     => '=Sheet8!$D$55:$D$'.($num81+54),
#    name       => '�����������',
#);
#$chart81->set_title( name => '������ ����������� ����������' );
#$chart81->set_x_axis( name => '���' );
#$chart81->set_y_axis( name => '���������� ����������' );
#$worksheet8->insert_chart( 'A4', $chart81 );

###############################################################################
#��������� ����������


$worksheet9->merge_range('A4:H4', '���������� ����������.', $title);

my $headings91 = [ '�����', '���-��', '�������������', '�������������', '�����������'];

$worksheet9->write( 'B6', $headings91, $boldborder );
$worksheet9->write( 'B7', $import->{9}->{loc}, $border );
$worksheet9->write( 'H25', $import->{9}->{top_loc}, $border );

my $chart91 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart91->set_style( 10 );
$chart91->add_series(
    categories => '=\'��������� ����������\'!$H$25:$H$30',
    values     => '=\'��������� ����������\'!$I$25:$I$30',
    name       => '��������� ��������� ����������',
);
$chart91->set_title( name => '��������� ����������' );
$worksheet9->insert_chart( 'H6', $chart91 );

###############################################################################
#�������������� ���������

$worksheet10->write( 'B4', '��������� ���������� �������������� ������� ����������.', $bold );

my $headings101 = [ '���������� ������', '���-��', '�������������', '�������������', '������������' ];
my $data101 = [
	['9-14','15-20','21-26','27-32','33-38','39-44','45-50','51-56','57-62','63-68'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
];
$worksheet10->write( 'B20', $headings101, $boldborder );
$worksheet10->write( 'B21', $import->{10}->{age}, $border );

$worksheet10->write( 'B32', '���������� ���������� ����� �� �����������.', $bold );

$worksheet10->write( 'B49', '������� �������������� ������� ���������.', $bold );
my $headings102 = [ '���', '���������� ����������', '�������������', '�������������', '������������' ];
$worksheet10->write( 'B50', $headings102, $boldborder );
$worksheet10->write( 'B51', $import->{10}->{gen}, $border );

#my $headings103 = [ '�����', '�����������', '�������' ];
#my $headings103 = [ ['�����'], ['�����������'], ['�������'] ];
#$worksheet10->write( 'L10', $headings103, $boldborder );

$worksheet10->write( 'H10', ['����� ��������� �� ��������'], $bold );
$worksheet10->write( 'H11', $import->{10}->{value_mas}, $border );

my $chart101 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart101->set_style( 10 );
$chart101->add_series(
    categories => '=\'�������������� ���������\'!$B$21:$B$29',
    values     => '=\'�������������� ���������\'!$C$21:$C$29',
    name       => '��������� ���������� �������������� ������� ����������',
);
$chart101->set_title( name => '���������� ���������' );
$worksheet10->insert_chart( 'B5', $chart101 );

my $chart102 = $workbook->add_chart( type => 'line', embedded => 1 );
$chart102->set_style( 10 );
#$chart102->add_series(
#    categories => '=\'�������������� ���������\'!$B$21:$B$29',
#    values     => '=\'�������������� ���������\'!$C$21:$C$29',
#    name       => '����� ���-��',               
#);   
$chart102->add_series(
    categories => '=\'�������������� ���������\'!$B$21:$B$29',
    values     => '=\'�������������� ���������\'!$C$21:$C$30',
    name       => '����� ���-��',         
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart102->add_series(
    categories => '=\'�������������� ���������\'!$B$21:$B$29',
    values     => '=\'�������������� ���������\'!$E$21:$E$29',
    name       => '�������������',               
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart102->add_series(
    categories => '=\'�������������� ���������\'!$B$21:$B$29',
    values     => '=\'�������������� ���������\'!$D$21:$D$30',
    name       => '�������������',               
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);
$chart102->add_series(
    categories => '=\'�������������� ���������\'!$B$21:$B$29',
    values     => '=\'�������������� ���������\'!$F$21:$F$29',
    name       => '�����������',               
	marker     => { type => 'circle', size => 5 },
	line       => { width => 2.25 },
);                            
$chart102->set_title( name => '���������� ���������� ����� �� �����������' );
$chart102->set_x_axis( name => '���' );
$chart102->set_y_axis( name => '���������� ����������' );
$worksheet10->insert_chart( 'B33', $chart102 );

my $chart103 = $workbook->add_chart( type => 'pie', embedded => 1  );
$chart103->set_style( 10 );
$chart103->add_series(
    categories => '=\'�������������� ���������\'!$B$51:$B$52',
    values     => '=\'�������������� ���������\'!$C$51:$C$52',
    name       => '',
);
$chart103->set_title( name => '���' );
$worksheet10->insert_chart( 'B53', $chart103 );

###############################################################################

my $logoformat = $workbook->add_format(
                                    border      => 0,
                                    align       => 'left',
                                    valign      => 'bottom',
                                  );


								#$worksheet1->merge_range('B1:H2', 'e-mail:  bma@wobot.ru    ������ ���������: +7 (901) 556-06-44', $logoformat);
								$worksheet1->insert_image('A1', '/var/www/project/excel/logo.png');
								
								#$worksheet2->merge_range('B1:H2', '', $logoformat);
								$worksheet2->insert_image('A1', '/var/www/project/excel/logo.png');
								$worksheet2->write('B1', 'internal:�����!A1','�����',$link);

								#$worksheet3->merge_range('B1:H2', '', $logoformat);
								$worksheet3->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet3->merge_range('A4:O4', '����������', $bold);
								$worksheet3->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet4->merge_range('B1:H2', '', $logoformat);
								$worksheet4->write('B1', 'internal:�����!A1','�����',$link);
								#$worksheet4->merge_range('A5:G5', '���������� �����(���-10)', $bold);
								$worksheet4->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet5->merge_range('B1:H2', '', $logoformat);
								$worksheet5->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet5->merge_range('B3:E3', '������������� ���������� �� �������� � ���������', $bold);
								$worksheet5->merge_range('B23:D23', '������������� ���������� �� ��������', $bold);
								$worksheet5->merge_range('E41:J41', '������������� ���������� �� ����(���-5 ��������)', $bold);
								$worksheet5->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet6->merge_range('B1:H2', '', $logoformat);
								$worksheet6->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet6->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet7->merge_range('B1:H2', '', $logoformat);
								$worksheet7->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet7->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet8->merge_range('B1:H2', '', $logoformat);
								$worksheet8->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet8->merge_range('A25:B25', '��� 10 ���������� (�� ������)', $bold);
								$worksheet8->merge_range('A40:B40', '��� 10 ���������� (�� ������)', $bold);
								$worksheet8->merge_range('A53:B53', '����������� ���������� �� ����', $bold);
								$worksheet8->merge_range('F53:G53', '����������� ����������', $bold);
								$worksheet8->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet9->merge_range('B1:H2', '', $logoformat);
								$worksheet9->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet9->insert_image('A1', '/var/www/project/excel/logo.png');

								#$worksheet10->merge_range('B1:H2', '', $logoformat);
								$worksheet10->write('B1', 'internal:�����!A1','�����',$link);
								$worksheet10->merge_range('B32:D32', '���������� ���������� ����� �� �����������.', $bold);
								$worksheet10->merge_range('B49:F49', '������� �������������� ������� ����������.', $bold);
								$worksheet10->insert_image('A1', '/var/www/project/excel/logo.png');


__END__

