#!/usr/bin/perl -w

###############################################################################


#use utf8;
use JSON::XS;
use Data::Dump qw(dump);
# use Math::Round;

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

my $bold      = $workbook->add_format( bold => 1 );
my $mbold      = $workbook->add_format( bold => 1 );
my $boldborder      = $workbook->add_format( bold => 1, border => 1 );
my $border      = $workbook->add_format( border => 1 );
my $alignrightbold      = $workbook->add_format( align => 'right', bold => 1 );
my $alignleft = $workbook->add_format( align => 'left' );
$alignleft->set_text_wrap();
my $link = $workbook->add_format();
$link->set_size(12);
$link->set_bold();
$link->set_color('green');
$link->set_underline();
my $link2 = $workbook->add_format();
$link2->set_size(12);
$link2->set_color('blue');
$link2->set_underline();
my $linkwrap = $workbook->add_format();
$linkwrap->set_size(12);
$linkwrap->set_color('blue');
$linkwrap->set_underline();
$linkwrap->set_text_wrap();
my $title = $workbook->add_format(
                                    border      => 0,
                                    bold        => 1,
                                    align       => 'center',
                                    valign      => 'vcenter',
                                  );

if ($import->{analytics})
{
    my $worksheet1 = $workbook->add_worksheet('Indicators');
    $worksheet1->set_landscape();
    my $worksheet2 = $workbook->add_worksheet('Data for indicators');

    $worksheet1->set_column('A:A', 5);  
    $worksheet1->set_column('B:B', 8); 
    $worksheet1->set_column('C:C', 8); 
    $worksheet1->set_column('D:D', 55); 
    $worksheet1->set_column('E:E', 20); 
    $worksheet1->set_column('F:F', 5); 
    $worksheet1->set_column('G:G', 8); 

    $worksheet1->insert_image('A1', '/var/www/project/excel/logo.png');
    $worksheet1->write( 'D1', 'Wobot - monitoring and analytics of social media', $bold );
    $worksheet1->write( 'D8', 'Social media monitoring report', $workbook->add_format( size => 18, bold => 1 ) );
    $worksheet1->write( 'B12', 'Topic:' );
    $worksheet1->write( 'B13', 'Period:' );
    $worksheet1->write( 'B14', 'Search query:' );
    $worksheet1->write( 'D12', $import->{1}->{order_name} );
    $worksheet1->write( 'D13', $import->{1}->{start}.'-'.$import->{1}->{end} );
    $worksheet1->write( 'D14', $import->{1}->{order_keyword}, $alignleft );
    $worksheet1->write( 'A33', 'email: mail@wobot.ru', $bold );
    $worksheet1->write( 'D33', 'Support service', $alignrightbold );
    $worksheet1->write( 'E33', '+7 968 531-79-73', $bold );
    $worksheet1->write( 'D35', 'The main indicators', $workbook->add_format( size => 18, bold => 1 ) );
    $worksheet1->write( 'D37', 'Number of mentions');
    $worksheet1->write( 'D38', 'Number of unique mentions');
    $worksheet1->write( 'D39', 'Unique authors');
    $worksheet1->write( 'D40', 'Average mentions per day');
    $worksheet1->write( 'D41', 'Number of resource');
    $worksheet1->write( 'D42', 'Reach');
    $worksheet1->write( 'D43', 'Engagement including');
    $worksheet1->write( 'D44', 'Number of likes');
    $worksheet1->write( 'D45', 'Number of comments');
    $worksheet1->write( 'D46', 'Number of retweets');
    $worksheet1->write( 'D47', 'Emotional mentions');
    $worksheet1->write( 'D48', 'Positive');
    $worksheet1->write( 'D49', 'Negative');
    $worksheet1->write( 'E37', $import->{1}->{count_post});
    $worksheet1->write( 'E38', $import->{1}->{uniq_post});
    $worksheet1->write( 'E39', $import->{1}->{count_blogs});
    $worksheet1->write( 'E40', $import->{1}->{post_per_day});
    $worksheet1->write( 'E41', $import->{1}->{count_host});
    $worksheet1->write( 'E42', $import->{1}->{audience});
    $worksheet1->write( 'E43', $import->{1}->{engage});
    $worksheet1->write( 'E44', $import->{1}->{count_likes});
    $worksheet1->write( 'E45', $import->{1}->{count_comment});
    $worksheet1->write( 'E46', $import->{1}->{count_retweet});
    $worksheet1->write( 'E47', $import->{1}->{nastr});
    $worksheet1->write( 'E48', $import->{1}->{positive});
    $worksheet1->write( 'E49', $import->{1}->{negative});

    my $chart1 = $workbook->add_chart( type => 'line', embedded => 1, border => { none  => 0 } );
    $chart1->set_style( 10 );
    my $num = @{$import->{2}->{din_post}[0]}-1;
    $chart1->add_series(
        categories => '=\'Data for indicators\'!$A$2:$A$'.($num+2),
        values     => '=\'Data for indicators\'!$B$2:$B$'.($num+2),
        name       => 'All mentions',
        marker     => { type => 'circle', size => 5 },
        line       => { width => 1.75 },
    );
    $chart1->add_series(
        categories => '=\'Data for indicators\'!$A$2:$A$'.($num+2),
        values     => '=\'Data for indicators\'!$C$2:$C$'.($num+2),
        name       => 'Mentions without doubles',
        marker     => { type => 'circle', size => 5 },
        line       => { width => 1.75 },
    );

    # Add some labels.
    $chart1->set_title( name => 'Mentions dynamics', name_font => { size => 14 } );
    $chart1->set_x_axis( name => 'Days' );
    $chart1->set_y_axis( name => 'Number of mentions' );
    $chart1->set_chartarea( 
        border => { 
            none  => 1 
            },
    );

    # Insert the chart into the main worksheet.
    $worksheet1->insert_chart( 'A52', $chart1, 0, 0, 1.65, 1 );

    $worksheet1->write( 'B69', 'Sources', $workbook->add_format( size => 18, bold => 1 ) );

    my $chart2 = $workbook->add_chart( type => 'pie', embedded => 1, border => { none  => 0 }  );
    $chart2->set_style( 10 );
    $chart2->add_series(
        categories => '=\'Data for indicators\'!$S$3:$S$'.(2+@{$import->{2}->{mhost_proc}[0]}),
        values     => '=\'Data for indicators\'!$T$3:$T$'.(2+@{$import->{2}->{mhost_proc}[0]}),
        name       => 'Resources',
        marker     => { type => 'circle', size => 5 },
        data_labels => {
                percentage   => 1,
                leader_lines => 1,
                position     => 'inside_end'
            },
        line       => { width => 0.5 },
    );
    $chart2->set_title( name => 'Resources', name_font => { size => 14 } );
    $chart2->set_chartarea( border => { none  => 0 } );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'A71', $chart2, 0, 0, 0.8, 1 );

    my $chart2 = $workbook->add_chart( type => 'pie', embedded => 1  );
    $chart2->set_style( 10 );
    $chart2->add_series(
        categories => '=\'Data for indicators\'!$AE$4:$AE$'.(2+@{$import->{2}->{mtype_all}[0]}),
        values     => '=\'Data for indicators\'!$AF$4:$AF$'.(2+@{$import->{2}->{mtype_all}[0]}),
        name       => 'Resources',
        marker     => { type => 'circle', size => 5 },
        data_labels => {
                percentage   => 1,
                leader_lines => 1,
                position     => 'inside_end'
            },
        line       => { width => 0.5 },
    );
    $chart2->set_title( name => 'Types of sources', name_font => { size => 14 } );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'D71', $chart2, 230, 0, 0.8, 1 );

    my $chart3 = $workbook->add_chart( type => 'line', embedded => 1 );
    $chart3->set_style( 10 );
    # Add some labels.
    $chart3->set_title( name => 'Mentions dynamics on three main resources', name_font => { size => 14 } );
    $chart3->set_x_axis( name => 'Days' );
    $chart3->set_y_axis( name => 'Number of mentions' );
    my $num52 = @{$import->{2}->{din_top_host}[0]};
    $chart3->add_series(
        categories => '=\'Data for indicators\'!$D$3:$D$'.($num52+1),
        values     => '=\'Data for indicators\'!$E$3:$E$'.($num52+1),
        name       => $import->{2}->{din_top_host}[1][0],
        marker     => { type => 'circle', size => 5 },
        line       => { width => 1.75 },
    );
    $chart3->add_series(
        categories => '=\'Data for indicators\'!$D$3:$D$'.($num52+1),
        values     => '=\'Data for indicators\'!$F$3:$F$'.($num52+1),
        name       => $import->{2}->{din_top_host}[2][0],
        marker     => { type => 'circle', size => 5 },
        line       => { width => 1.75 },
    );
    $chart3->add_series(
        categories => '=\'Data for indicators\'!$D$3:$D$'.($num52+1),
        values     => '=\'Data for indicators\'!$G$3:$G$'.($num52+1),
        name       => $import->{2}->{din_top_host}[3][0],
        marker     => { type => 'circle', size => 5 },
        line       => { width => 1.75 },
    );

    $chart3->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    # Insert the chart into the main worksheet.
    $worksheet1->insert_chart( 'A86', $chart3, 0, 0, 1.65, 1 );

    $worksheet1->write( 'B104', 'Sentiment', $workbook->add_format( size => 18, bold => 1 ) );

    my $chart4 = $workbook->add_chart( type => 'pie', embedded => 1  );
    $chart4->set_style( 10 );
    $chart4->add_series(
        categories => '=\'Data for indicators\'!$P$4:$P$6',
        values     => '=\'Data for indicators\'!$Q$4:$Q$6',
        name       => 'Sentiment by resources',
        marker     => { type => 'circle', size => 5 },
        data_labels => {
                percentage   => 1,
                # value => 1,
                leader_lines => 1,
                position     => 'inside_end'
            },
        line       => { width => 0.5 },
    );
    $chart4->set_title( name => 'Sentiment of mentions',name_font => { size => 14 } );
    $chart4->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'A106', $chart4, 0, 0, 0.8, 0.7 );

    my $chart2 = $workbook->add_chart(
        type     => 'bar',
        embedded => 1,
        subtype  => 'percent_stacked'
    );
    my $numchart2=@{$import->{2}->{mhost_proc_nastr}[0]}-1;
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$W$3',
        categories => '=\'Data for indicators\'!$V$4:$V$'.(3+$numchart2),
        values     => '=\'Data for indicators\'!$W$4:$W$'.(3+$numchart2),
        fill       => { color => '#d46661' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$X$3',
        categories => [ '\'Data for indicators\'', 3, (2+$numchart2), 21, 21 ],
        values     => [ '\'Data for indicators\'', 3, (2+$numchart2), 23, 23 ],
        fill       => { color => '#cbcbcb' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$Y$3',
        categories => [ '\'Data for indicators\'', 3, (2+$numchart2), 21, 21 ],
        values     => [ '\'Data for indicators\'', 3, (2+$numchart2), 24, 24 ],
        fill       => { color => '#a8c572' },
    );
    $chart2->set_title ( name => 'Sentiment by resources', name_font => { size => 14 } );
    $chart2->set_x_axis( name => '' );
    $chart2->set_y_axis( name => '' );
    $chart2->set_style( 10 );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'D106', $chart2, 230, 0, 0.8, 1 );

    # $worksheet1->write( 'B113', 'Доля сообщений', $bold);
    # $worksheet1->write( 'B114', 'Позитивные');
    # $worksheet1->write( 'B115', 'Нейтральные');
    # $worksheet1->write( 'B116', 'Негативные');
    # $worksheet1->write( 'C114', round($import->{1}->{positive}*100/$import->{1}->{count_post}).'%');
    # $worksheet1->write( 'C115', round($import->{1}->{neutral}*100/$import->{1}->{count_post}).'%');
    # $worksheet1->write( 'C116', round($import->{1}->{negative}*100/$import->{1}->{count_post}).'%');
    $worksheet1->write( 'B116', 'NSI', $bold );
    $worksheet1->write( 'C116', int(($import->{1}->{positive}+$import->{1}->{neutral}-$import->{1}->{negative})*100/$import->{1}->{count_post})/100, $bold );

    my $chart = $workbook->add_chart( type => 'area', embedded => 1, subtype  => 'percent_stacked' );
    my $num_chart = @{$import->{2}->{din_nastr}[0]}-2;
    $chart->add_series(
        name       => '=\'Data for indicators\'!$M$2',
        categories => '=\'Data for indicators\'!$I$3:$I$'.(3+$num_chart),
        values     => '=\'Data for indicators\'!$M$3:$M$'.(3+$num_chart),
        fill       => { color => '#d46661' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart->add_series(
        name       => '=\'Data for indicators\'!$N$2',
        categories => [ '\'Data for indicators\'', 2, (2+$num_chart), 8, 8 ],
        values     => [ '\'Data for indicators\'', 2, (2+$num_chart), 13, 13 ],
        fill       => { color => '#cbcbcb' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart->add_series(
        name       => '=\'Data for indicators\'!$O$2',
        categories => [ '\'Data for indicators\'', 2, (2+$num_chart), 8, 8 ],
        values     => [ '\'Data for indicators\'', 2, (2+$num_chart), 14, 14 ],
        fill       => { color => '#a8c572' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart->set_title ( name => 'Sentiment dynamics', name_font => { size => 14 } );
    $chart->set_x_axis( name => 'Days' );
    $chart->set_y_axis( name => 'Number of mentions' );
    $chart->set_style( 10 );
    $chart->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'A121', $chart, 0, 0, 1.65, 0 );

    $worksheet1->write( 'B139', 'Geography', $workbook->add_format( size => 18, bold => 1 ));

    my $num_chart = @{$import->{2}->{mloc_all}[0]}-1;
    my $chart2 = $workbook->add_chart(
        type     => 'bar',
        embedded => 1,
    );
    $chart2->add_series(
        # name       => '=\'Data for indicators\'!$AH$1',
        categories => '=\'Data for indicators\'!$AH$4:$AH$'.(3+$num_chart),
        values     => '=\'Data for indicators\'!$AI$4:$AI$'.(3+$num_chart),
    );
    $chart2->set_style( 10 );
    $chart2->set_legend( position => 'none' );
    $chart->set_title ( name => 'Sentiment dynamics', name_font => { size => 14 } );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'B141', $chart2, 0, 0, 1, 1.7 );

    $worksheet1->write( 'B174', 'Subject of discussion', $workbook->add_format( size => 18, bold => 1 ));

    my $num_chart = @{$import->{2}->{mtag_all}[0]}-1;
    my $chart2 = $workbook->add_chart(
        type     => 'bar',
        embedded => 1,
        subtype  => 'stacked'
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$BA$3',
        categories => '=\'Data for indicators\'!$AZ$4:$AZ$'.(3+$num_chart),
        values     => '=\'Data for indicators\'!$BA$4:$BA$'.(3+$num_chart),
        fill       => { color => '#d46661' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$BB$3',
        categories => [ '\'Data for indicators\'', 3, (3+$num_chart), 51, 51 ],
        values     => [ '\'Data for indicators\'', 3, (3+$num_chart), 53, 53 ],
        fill       => { color => '#cbcbcb' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$BC$3',
        categories => [ '\'Data for indicators\'', 3, (3+$num_chart), 51, 51 ],
        values     => [ '\'Data for indicators\'', 3, (3+$num_chart), 54, 54 ],
        fill       => { color => '#a8c572' },
        line       => { width => 1.75 },
        marker     => { type => 'circle', size => 5 },
    );
    $chart2->set_style( 10 );
    # $chart2->set_legend( position => 'none' );
    # $chart->set_title ( name => 'Тематика обсуждения', name_font => { size => 14 } );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'B176', $chart2, 0, 0, 1, 2.0 );

    $worksheet1->write( 'B209', 'Demographic profile of audience', $workbook->add_format( size => 18, bold => 1 ));

    my $chart4 = $workbook->add_chart( type => 'pie', embedded => 1  );
    $chart4->add_series(
        categories => '=\'Data for indicators\'!$AR$4:$AR$6',
        values     => '=\'Data for indicators\'!$AV$4:$AV$6',
        name       => 'Gender',
        marker     => { type => 'circle', size => 5 },
        data_labels => {
                percentage   => 1,
                leader_lines => 1,
                position     => 'inside_end'
            },
        line       => { width => 0.5 },
    );
    $chart4->set_title( name => 'Gender', name_font => { size => 14 } );
    $chart4->set_style( 10 );
    $chart4->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'A211', $chart4, 0, 0, 0.8, 1 );

    my $chart4 = $workbook->add_chart( type => 'pie', embedded => 1  );
    $chart4->add_series(
        categories => '=\'Data for indicators\'!$AR$10:$AR$14',
        values     => '=\'Data for indicators\'!$AV$10:$AV$14',
        name       => 'Распределение аудитории по полу',
        marker     => { type => 'circle', size => 5 },
        data_labels => {
                percentage   => 1,
                leader_lines => 1,
                position     => 'inside_end'
            },
        line       => { width => 0.5 },
    );
    $chart4->set_title( name => 'Age', name_font => { size => 14 } );
    $chart4->set_style( 10 );
    $chart4->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'D211', $chart4, 230, 0, 0.8, 1 );

    my $chart2 = $workbook->add_chart(
        type     => 'column',
        embedded => 1,
        subtype  => 'percent_stacked'
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AU$3',
        categories => '=\'Data for indicators\'!$AR$4:$AR$6',
        values     => '=\'Data for indicators\'!$AU$4:$AU$6',
        fill       => { color => '#d46661' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AT$3',
        categories => [ '\'Data for indicators\'', 3, 5, 43, 43 ],
        values     => [ '\'Data for indicators\'', 3, 5, 45, 45 ],
        fill       => { color => '#cbcbcb' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AS$3',
        categories => [ '\'Data for indicators\'', 3, 5, 43, 43 ],
        values     => [ '\'Data for indicators\'', 3, 5, 44, 44 ],
        fill       => { color => '#a8c572' },
    );
    $chart2->set_title ( name => 'Sentiment by gender', name_font => { size => 14 } );
    $chart2->set_x_axis( name => '' );
    $chart2->set_y_axis( name => '' );
    $chart2->set_style( 10 );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'A225', $chart2, 0, 0, 0.8, 1 );

    my $chart2 = $workbook->add_chart(
        type     => 'column',
        embedded => 1,
        subtype  => 'percent_stacked'
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AU$3',
        categories => '=\'Data for indicators\'!$AR$10:$AR$14',
        values     => '=\'Data for indicators\'!$AU$10:$AU$14',
        fill       => { color => '#d46661' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AT$3',
        categories => [ '\'Data for indicators\'', 9, 13, 43, 43 ],
        values     => [ '\'Data for indicators\'', 9, 13, 45, 45 ],
        fill       => { color => '#cbcbcb' },
    );
    $chart2->add_series(
        name       => '=\'Data for indicators\'!$AS$3',
        categories => [ '\'Data for indicators\'', 9, 13, 43, 43 ],
        values     => [ '\'Data for indicators\'', 9, 13, 44, 44 ],
        fill       => { color => '#a8c572' },
    );
    $chart2->set_title ( name => 'Sentiment by age', name_font => { size => 14 } );
    $chart2->set_x_axis( name => '' );
    $chart2->set_y_axis( name => '' );
    $chart2->set_style( 10 );
    $chart2->set_chartarea( 
        border => { 
            none  => 1 
            },
    );
    $worksheet1->insert_chart( 'D225', $chart2, 230, 0, 0.8, 1 );

    $worksheet1->write( 'B243', 'Popular mentions (Top-10)', $workbook->add_format( size => 18, bold => 1 ));
    $worksheet1->write( 'A244', [ '№', 'Date', 'Time', 'Text', 'Link','Engagement','Reach'], $border );
    # $worksheet1->write( 'A196', $import->{1}->{top_post}, $border);

    $worksheet1->write( 'A245', [$import->{1}->{top_post}[0]] );
    $worksheet1->write( 'B245', [$import->{1}->{top_post}[1]] );
    $worksheet1->write( 'C245', [$import->{1}->{top_post}[2]] );
    $worksheet1->write( 'D245', [$import->{1}->{top_post}[3]] );
    $worksheet1->write( 'E245', [$import->{1}->{top_post}[4]] );
    $worksheet1->write( 'F245', [$import->{1}->{top_post}[5]] );
    $worksheet1->write( 'G245', [$import->{1}->{top_post}[6]] );
    # $result = [ $result ]
    my $iter=0;
    # $url_info = [ $import->{3}[5] ];
    foreach my $name (@{$import->{1}->{top_post}[3]}) {
        # $worksheet3->write( 'A'.($iter+6), $name );
        # $worksheet1->write_url( 'D'.($iter+245), $import->{1}->{top_post}[4][$iter], $name , $linkwrap);
        $iter++;
    }

    $worksheet1->write( 'B269', 'Top 10 positive (by reach)', $workbook->add_format( size => 18, bold => 1 ));
    $worksheet1->write( 'A270', [ '№', 'Date', 'Time', 'Text', 'Link','Engagement','Reach'], $border );
    # $worksheet1->write( 'A216', $import->{1}->{top_post_positive}, $border);
    $worksheet1->write( 'A271', [$import->{1}->{top_post_positive}[0]] );
    $worksheet1->write( 'B271', [$import->{1}->{top_post_positive}[1]] );
    $worksheet1->write( 'C271', [$import->{1}->{top_post_positive}[2]] );
    $worksheet1->write( 'D271', [$import->{1}->{top_post_positive}[3]] );
    $worksheet1->write( 'E271', [$import->{1}->{top_post_positive}[4]] );
    $worksheet1->write( 'F271', [$import->{1}->{top_post_positive}[5]] );
    $worksheet1->write( 'G271', [$import->{1}->{top_post_positive}[6]] );
    # $result = [ $result ]
    my $iter=0;
    # $url_info = [ $import->{3}[5] ];
    foreach my $name (@{$import->{1}->{top_post_positive}[3]}) {
        # $worksheet3->write( 'A'.($iter+6), $name );
        # $worksheet1->write_url( 'D'.($iter+271), $import->{1}->{top_post_positive}[4][$iter], $name , $linkwrap);
        $iter++;
    }

    $worksheet1->write( 'B303', 'Top 10 negative (by reach)', $workbook->add_format( size => 18, bold => 1 ));
    $worksheet1->write( 'A304', [ '№', 'Date', 'Time', 'Text', 'Link','Engagement','Reach'], $border );
    # $worksheet1->write( 'A235', $import->{1}->{top_post_negative}, $border);
    $worksheet1->write( 'A305', [$import->{1}->{top_post_negative}[0]] );
    $worksheet1->write( 'B305', [$import->{1}->{top_post_negative}[1]] );
    $worksheet1->write( 'C305', [$import->{1}->{top_post_negative}[2]] );
    $worksheet1->write( 'D305', [$import->{1}->{top_post_negative}[3]] );
    $worksheet1->write( 'E305', [$import->{1}->{top_post_negative}[4]] );
    $worksheet1->write( 'F305', [$import->{1}->{top_post_negative}[5]] );
    $worksheet1->write( 'G305', [$import->{1}->{top_post_negative}[6]] );
    # $result = [ $result ]
    my $iter=0;
    # $url_info = [ $import->{3}[5] ];
    foreach my $name (@{$import->{1}->{top_post_negative}[3]}) {
        # $worksheet3->write( 'A'.($iter+6), $name );
        # $worksheet1->write_url( 'D'.($iter+305), $import->{1}->{top_post_negative}[4][$iter], $name , $linkwrap);
        $iter++;
    }

    $worksheet2->write( 'A2', $import->{2}->{din_post}, $border );
    $worksheet2->write( 'D2', $import->{2}->{din_top_host}, $border );
    $worksheet2->write( 'I2', $import->{2}->{din_nastr}, $border );
    $worksheet2->write( 'P3', $import->{2}->{nastr_all}, $border );
    $worksheet2->write( 'V3', $import->{2}->{mhost_proc_nastr}, $border );
    $worksheet2->write( 'S3', $import->{2}->{mhost_proc}, $border );
    $worksheet2->write( 'S13', $import->{2}->{mhost}, $border );
    $worksheet2->write( 'AE3', $import->{2}->{mtype_all}, $border );
    $worksheet2->write( 'AH3', $import->{2}->{mloc_all}, $border );
    $worksheet2->write( 'AK3', $import->{2}->{mloc_all_proc}, $border );
    $worksheet2->write( 'AR3', $import->{2}->{mgen_all}, $border );
    $worksheet2->write( 'AR9', $import->{2}->{mage_all}, $border );
    $worksheet2->write( 'AZ3', $import->{2}->{mtag_all}, $border );
}

if ($import->{mentions})
{
    my $worksheet3 = $workbook->add_worksheet('Posts');

    $worksheet3->set_column('A:D', 20); 
    $worksheet3->set_column('E:E', 25); 
    $worksheet3->set_column('F:F', 15); 
    $worksheet3->set_column('G:G', 50); 
    $worksheet3->set_column('H:K', 10); 
    $worksheet3->set_column('L:L', 15); 
    $worksheet3->set_column('M:O', 10); 
    $worksheet3->set_column('P:P', 20); 

    if ($import->{3}->{'export_digest'})
    {
        # $worksheet3->write( 'A2', $import->{3}->{all_post});
        $worksheet3->write( 'A1', ['Topic','Date','Time','Resource','Link','Type of sources','Full text','Didgest','Sentiment','Spam','Favourites','Engagement','Username','Gender','Age','Reach','Region','Tags'], $boldborder );
        if ($import->{3}->{tags_to_xlsx}!='null') 
        {
            $worksheet3->write( 'S1', $import->{3}->{tags_to_xlsx}, $boldborder );
        }
        $worksheet3->write( 'A2', [$import->{3}->{all_post}[0]], $border );
        $worksheet3->write( 'B2', [$import->{3}->{all_post}[1]], $border );
        $worksheet3->write( 'C2', [$import->{3}->{all_post}[2]], $border );
        $worksheet3->write( 'D2', [$import->{3}->{all_post}[3]], $border );
        $worksheet3->write( 'E2', [$import->{3}->{all_post}[4]], $border );
        $worksheet3->write( 'F2', [$import->{3}->{all_post}[5]], $border );
        $worksheet3->write( 'G2', [$import->{3}->{all_post}[6]], $border );
        # $worksheet1->write( 'I2', [$import->{3}->{all_post}[7]] );
        $worksheet3->write( 'I2', [$import->{3}->{all_post}[7]], $border );
        $worksheet3->write( 'J2', [$import->{3}->{all_post}[8]], $border );
        $worksheet3->write( 'K2', [$import->{3}->{all_post}[9]], $border );
        $worksheet3->write( 'L2', [$import->{3}->{all_post}[10]], $border );
        $worksheet3->write( 'M2', [$import->{3}->{all_post}[11]], $border );
        $worksheet3->write( 'N2', [$import->{3}->{all_post}[12]], $border );
        $worksheet3->write( 'O2', [$import->{3}->{all_post}[13]], $border );
        $worksheet3->write( 'P2', [$import->{3}->{all_post}[14]], $border );
        $worksheet3->write( 'Q2', [$import->{3}->{all_post}[15]], $border );
        $worksheet3->write( 'R2', [$import->{3}->{all_post}[16]], $border );
        if ($import->{3}->{tags_to_xlsx}!='null') 
        {
            $worksheet3->write( 'S2', $import->{3}->{posts_tags}, $border );
        }
        # $result = [ $result ]
        my $iter=0;
        # $url_info = [ $import->{3}[5] ];
        foreach my $name (@{$import->{3}->{all_post}[17]}) {
            # $worksheet3->write( 'A'.($iter+6), $name );
            $worksheet3->write_url( 'H'.($iter+2), $import->{3}->{all_post}[4][$iter], $name , $link2);
            $iter++;
        }
    }
    else
    {
        # $worksheet3->write( 'A2', $import->{3}->{all_post});
        $worksheet3->write( 'A1', ['Topic','Date','Time','Resource','Link','Type of sources','Full text','Sentiment','Spam','Favourites','Engagement','Username','Gender','Age','Reach','Region','Tags'],$boldborder );
        if ($import->{3}->{tags_to_xlsx}!='null') 
        {
            $worksheet3->write( 'R1', $import->{3}->{tags_to_xlsx},$boldborder);
        }
        $worksheet3->write( 'A2', [$import->{3}->{all_post}[0]], $border );
        $worksheet3->write( 'B2', [$import->{3}->{all_post}[1]], $border );
        $worksheet3->write( 'C2', [$import->{3}->{all_post}[2]], $border );
        $worksheet3->write( 'D2', [$import->{3}->{all_post}[3]], $border );
        $worksheet3->write( 'E2', [$import->{3}->{all_post}[4]], $border );
        $worksheet3->write( 'F2', [$import->{3}->{all_post}[5]], $border );
        $worksheet3->write( 'G2', [$import->{3}->{all_post}[6]], $border );
        $worksheet3->write( 'H2', [$import->{3}->{all_post}[7]], $border );
        $worksheet3->write( 'I2', [$import->{3}->{all_post}[8]], $border );
        $worksheet3->write( 'J2', [$import->{3}->{all_post}[9]], $border );
        $worksheet3->write( 'K2', [$import->{3}->{all_post}[10]], $border );
        $worksheet3->write( 'L2', [$import->{3}->{all_post}[11]], $border );
        $worksheet3->write( 'M2', [$import->{3}->{all_post}[12]], $border );
        $worksheet3->write( 'N2', [$import->{3}->{all_post}[13]], $border );
        $worksheet3->write( 'O2', [$import->{3}->{all_post}[14]], $border );
        $worksheet3->write( 'P2', [$import->{3}->{all_post}[15]], $border );
        $worksheet3->write( 'Q2', [$import->{3}->{all_post}[16]], $border );
        if ($import->{3}->{tags_to_xlsx}!='null') 
        {
            $worksheet3->write( 'R2', $import->{3}->{posts_tags}, $border );
        }
    }
}

if ($import->{authors})
{
    my $worksheet4 = $workbook->add_worksheet('Authors');
    
    $worksheet4->set_column('A:A', 10); 
    $worksheet4->set_column('B:C', 25); 
    $worksheet4->set_column('D:G', 10); 
    $worksheet4->set_column('H:H', 20); 
    $worksheet4->set_column('I:J', 10); 
    $worksheet4->set_column('K:K', 20); 
    $worksheet4->set_column('L:M', 10); 
    
    $worksheet4->write( 'A1', ['№','Speaker','Link to profile','Posts','Positive','Negative','Unknown','Resource','Gender','Age','Region','Reach','Total engagement'], $boldborder );
    $worksheet4->write( 'A2', $import->{4}->{blogs}, $border );
}

__END__

