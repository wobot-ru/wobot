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

my $workbook  = Spreadsheet::WriteExcel->new( '-' );
my $worksheet1 = $workbook->add_worksheet('Обзор');
my $worksheet2 = $workbook->add_worksheet();
my $worksheet3 = $workbook->add_worksheet();
my $worksheet4 = $workbook->add_worksheet();
my $worksheet5 = $workbook->add_worksheet();
my $worksheet6 = $workbook->add_worksheet();
my $worksheet7 = $workbook->add_worksheet();
my $worksheet8 = $workbook->add_worksheet();
my $worksheet9 = $workbook->add_worksheet();
my $worksheet10 = $workbook->add_worksheet();

my $bold      = $workbook->add_format( bold => 1 );
my $mbold      = $workbook->add_format( bold => 1 );
my $boldborder      = $workbook->add_format( bold => 1, border => 1 );
my $border      = $workbook->add_format( border => 1 );
my $link = $workbook->add_format();
$link->set_size(12);
$link->set_bold();
$link->set_color('green');
$link->set_underline();
my $title = $workbook->add_format(
                                    border      => 0,
									bold		=> 1,
                                    align       => 'center',
                                    valign      => 'vcenter',
                                  );





	$worksheet1->merge_range('A6:H6', 'Отчет по мониторингу.', $title);
	
	
	my $text1 = [
		[ 'Тема:', 'Период:', 'Поисковый запрос:' ],
		[ $import->{1}->{order_name}, $import->{1}->{order_time}, $import->{1}->{order_keyword} ],
	];
	
	$worksheet1->write( 'B7', $text1 );

	$worksheet1->merge_range('A11:H11', 'Содержание отчета:', $title);

	$worksheet1->write('B12', 'internal:Sheet1!A1','Обзор',$link);
	$worksheet1->write('B13', 'internal:Indicators!A1','Показатели',$link);
	$worksheet1->write('B14', 'internal:Sheet3!A1','Упоминания',$link);
	$worksheet1->write('B15', 'internal:Sheet4!A1',q{Популярные слова},$link);
	$worksheet1->write('B16', 'internal:Sheet5!A1','Ресурсы',$link);
	$worksheet1->write('B17', 'internal:Sheet6!A1','Спикеры',$link);
	$worksheet1->write('B18', 'internal:Sheet7!A1','Промоутеры',$link);
	$worksheet1->write('B19', 'internal:Sheet8!A1','Тональность',$link);
	$worksheet1->write('B20', 'internal:Sheet9!A1',q{География упоминаний},$link);
	$worksheet1->write('B21', 'internal:Sheet10!A1',q{Характеристики аудитории},$link);


##############################################################################

my $table2 = [
    [ 'Количество упомиинаний:', 'Уникальных авторов:', 'В среднем постов в сутки:', 'Количество ресурсов:', 'Аудитория:','Вовлеченность:'],
    [ $import->{2}->{count_posts}, $import->{2}->{uniq_auth}, $import->{2}->{post_in_day}, $import->{2}->{count_hosts}, $import->{2}->{audience}, $import->{2}->{engage} ],
];

#$worksheet2->set_column('B', 30);
#$worksheet2->set_column('C', 20);

$worksheet2->write( 'B4', $table2, $bold );

my $headings2 = [ 'Дата', 'Кол-во упоминаний', 'Теги' ];

$worksheet2->write( 'B33', $headings2, $boldborder );
$worksheet2->write( 'B34', $import->{2}->{graph}, $border );

my $chart2 = $workbook->add_chart( type => 'line', embedded => 1 );
my $num = @{$import->{2}->{graph}[0]};
$chart2->add_series(
    categories => '=Sheet2!$B$34:$B$'.($num+33),
    values     => '=Sheet2!$C$34:$C$'.($num+33),
    name       => $import->{1}->{order_name},
);

# Add some labels.
$chart2->set_title( name => 'График упоминаний' );
$chart2->set_x_axis( name => 'Дни' );
$chart2->set_y_axis( name => 'Количество упоминаний' );

# Insert the chart into the main worksheet.
$worksheet2->insert_chart( 'A13', $chart2 );

##############################################################################

my $headings3 = [ 'Дата', 'Ресурс', 'Ссылка', 'Тип ресурса', 'Полный текст', 'Тональность', 'Избранное', 'Спам', 'Engagement', 'Ник', 'Пол', 'Возраст', 'Аудитория', 'Регион', 'Теги' ];

#$worksheet3->set_column('A:O', 25);

$worksheet3->write( 'A5', $headings3, $bold );
$worksheet3->write( 'A6', $import->{3} );

###############################################################################

my $headings41 = [ 'Слово','Кол-во упоминаний'];
my $data41 = [
	['не хватает','дизайна','облака','тегов'],
	['10','5','3','2'],
];
$worksheet4->write( 'A5', $headings41, $boldborder );
$worksheet4->write( 'A6', $data41, $border );

$worksheet4->merge_range('F4:G4', 'Облако тегов', $mbold);

$worksheet4->merge_range('A25:E25', 'Популярные посты (Топ-10)', $mbold);

my $headings42 = [ '','Пост', 'Источник', 'Количество перепостов', 'Аудитория' ];
my $data42 = [
	['1','2','3','4','5'],
	['Осенняя мода в Анталии','Осенняя мода в Анталии','Осенняя мода в Анталии','Осенняя мода в Анталии','Осенняя мода в Анталии'],
	['antalyacity.ru','antalyacity.ru','antalyacity.ru','antalyacity.ru','antalyacity.ru'],
	['10','5','3','2','1'],
	['234','123','122','121','120'],
];
$worksheet4->write( 'A26', $headings42, $boldborder );
$worksheet4->write( 'A27', $import->{4}, $border );

###############################################################################

my $data51 = [
	['twitter.com','livejournal.com','vkontakte.ru','ya.ru','rutwit.ru','blogs.mail.ru','blogspot.com','diary.ru','jujuju.ru','google.com'],
	['60%','10%','5%','5%','5%','5%','5%','5%','5%','5%'],
];
$worksheet5->write( 'E4', $import->{5}->{proc}, $border );

my $headings52 = [ 'Ресурс', 'Количество упоминаний' ];
my $data52 = [
	['twitter.com','livejournal.com','vkontakte.ru','ya.ru','rutwit.ru','blogs.mail.ru','blogspot.com','diary.ru','jujuju.ru','google.com'],
	['600','100','50','50','50','50','50','50','50','50'],
];
$worksheet5->write( 'B20', $headings52, $boldborder );
$worksheet5->write( 'B21', $import->{5}->{count}, $border );

my $headings53 = [ 'Дата', 'Количество упоминаний','twitter.com','livejournal.com','vkontakte.ru','ya.ru','rutwit.ru','blogs.mail.ru','blogspot.com','diary.ru','jujuju.ru','google.com'];
my $data53 = [
	['01-11-2011','02-11-2011','03-11-2011','04-11-2011','05-11-2011','06-11-2011','07-11-2011','08-11-2011','09-11-2011','10-11-2011'],
	['1000','1000','1000','1000','1000','1000','1000','1000','1000','1000'],
	['600','600','600','600','600','600','600','600','600','600'],
	['100','100','100','100','100','100','100','100','100','100'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
	['50','50','50','50','50','50','50','50','50','50'],
];
$worksheet5->write( 'B53', $headings53, $boldborder );
$worksheet5->write( 'B54', $import->{5}->{top_count}, $border );

###############################################################################

my $headings61 = [ '№', 'Спикер','Посты', 'Число читателей','Позитивных','Негативных','Неопределено','Название блога', 'Ресурс' ];
my $data61 = [

	['1','2','3','4','5','6','7','8','9','10'],
	['_rcp','polynchyk','avd','bma','dolbaeb','xz','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['_rcp','polynchyk','avd','bma','dolbaeb','xz','7','8','9','10'],
	['twitter.com','livejournal.com','vkontakte.ru','ya.ru','rutwit.ru','blogs.mail.ru','blogspot.com','diary.ru','jujuju.ru','google.com'],
];
$worksheet6->write( 'A4', $headings61, $boldborder );
$worksheet6->write( 'A5', $import->{6}, $border );

###############################################################################

my $headings71 = [ '№', 'Промоутеры', 'Число читателей', 'Посты', 'Позитивных', 'Негативных', 'Неопределено', 'Название блога', 'Ресурс' ];
my $data71 = [

	['1','2','3','4','5','6','7','8','9','10'],
	['_rcp','polynchyk','avd','bma','dolbaeb','xz','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],	
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['_rcp','polynchyk','avd','bma','dolbaeb','xz','7','8','9','10'],
	['twitter.com','livejournal.com','vkontakte.ru','ya.ru','rutwit.ru','blogs.mail.ru','blogspot.com','diary.ru','jujuju.ru','google.com'],
];
$worksheet7->write( 'A4', $headings71, $boldborder );
$worksheet7->write( 'A5', $import->{7}, $border );

###############################################################################

my $headings81 = [ 'Дата', 'Позитивные', 'Негативные', 'Нейтральные', 'Неопределенные' ];
my $data81 = [
	['01-11-2011','02-11-2011','03-11-2011','04-11-2011','05-11-2011','06-11-2011','07-11-2011','08-11-2011','09-11-2011','10-11-2011'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],	
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
];
$worksheet8->write( 'A54', $headings81, $boldborder );
$worksheet8->write( 'A55', $import->{8}->{dinams}, $border );

my $headings82 = [ '', 'Кол-во постов' ];
my $data82 = [
	['Позитивные', 'Негативные', 'Нейтральные'],
	['5%','10%','85%'],
];
$worksheet8->write( 'G67', $headings82, $boldborder );
$worksheet8->write( 'G68', $import->{8}->{all}, $border );

$worksheet8->write( 'A25', 'Топ 10 Позитивных', $bold );
my $headings83 = [ 'Дата', 'Ресурс', 'Ссылка', 'Тип ресурса', 'Полный текст', 'Engagement', 'Ник', 'Пол', 'Возраст', 'Аудитория', 'Регион', 'Теги'];
my $data83 = [
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
];
$worksheet8->write( 'A26', $headings83, $boldborder );
$worksheet8->write( 'A27', $import->{8}->{top_positive}, $border );

$worksheet8->write( 'A40', 'Топ 10 Позитивных', $bold );
my $headings84 = [ 'Дата', 'Ресурс', 'Ссылка', 'Тип ресурса', 'Полный текст', 'Engagement', 'Ник', 'Пол', 'Возраст', 'Аудитория', 'Регион', 'Теги'];
my $data84 = [
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
];
$worksheet8->write( 'A41', $headings84, $boldborder );
$worksheet8->write( 'A42', $import->{8}->{top_negative}, $border );

###############################################################################

$worksheet9->merge_range('A4:H4', 'Георграфия упоминаний.', $title);

my $headings91 = [ 'Город', 'Количество упомнаний', 'Положительных', 'Отрицательных', 'Нейтральных'];
my $data91 = [
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
	['1','2','3','4','5','6','7'],
];
$worksheet9->write( 'A24', $headings91, $boldborder );
$worksheet9->write( 'A25', $import->{9}, $border );

###############################################################################

$worksheet10->write( 'B4', 'Диаграмма возрастной принадлежности авторов упоминаний.', $bold );

my $headings101 = [ 'Возростная группа', 'Количество упоминаний', 'Положительных', 'Отрицательных', 'Неопределено' ];
my $data101 = [
	['9-14','15-20','21-26','27-32','33-38','39-44','45-50','51-56','57-62','63-68'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
	['1','2','3','4','5','6','7','8','9','10'],
];
$worksheet10->write( 'B15', $headings101, $boldborder );
$worksheet10->write( 'B16', $import->{10}->{age}, $border );

$worksheet10->write( 'B27', 'Активность возрастных групп по тональности.', $bold );

$worksheet10->write( 'B44', 'Половая принадлежность авторов упомнаний.', $bold );
my $headings102 = [ 'Пол', 'Количество упоминаний', 'Положительных', 'Отрицательных', 'Неопределено' ];
my $data102 = [
	['Мужчина','Женщина'],
	['56','43'],
	['16','34'],
	['3','0'],
	['0','45'],
];
$worksheet10->write( 'B45', $headings102, $boldborder );
$worksheet10->write( 'B46', $import->{10}->{gen}, $border );

###############################################################################

my $logoformat = $workbook->add_format(
                                    border      => 0,
                                    align       => 'left',
                                    valign      => 'bottom',
                                  );

								$worksheet1->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet1->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);
								$worksheet1->set_column(1,1, 40);	

								$worksheet2->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet2->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet3->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet3->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet4->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet4->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet5->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet5->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet6->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet6->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet7->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet7->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet8->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet8->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet9->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet9->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

								$worksheet10->merge_range('A1:H2', 'e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44', $logoformat);
								$worksheet10->insert_image('A1', '/var/www/project/excel/logo.png', 0, 0, .5, .5);

__END__

