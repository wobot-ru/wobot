# coding=utf-8
from xlsxwriter.workbook import Workbook
import re

class Template:
    review_comments = [u"На которых мы нашли упоминания по вашей теме",
                       u"Количество подписчиков или друзей у авторов упоминаний",
                       u"По социальным сетям Facebook и Вконтакте",
                       u"К упоминаниям из выдачи. Учитываются комментарии с популярных ресурсов,"
                       u" например, livejournal.ru, vk.com",
                       u"По микроблогу Twitter", u"Рассчитано по XX% упоминаний, для которых удалось определить регион"]

    summary_table = [u"Упоминаний", u"Уникальных авторов", u"Среднее количество постов в сутки", u"Ресурсов",
                     u"Охват", u"Лайков", u"Комментариев", u"Ретвитов", u"Регионов"]
    time_distribution_column = [unicode(x) for x in range(24)]
    mentions_table = [u"Дата", u"Время", u"Ресурс", u"Текст упоминания", u"Дайджест", u"Ссылка", u"Лайки",
                      u"Комментарии", u"Ретвитты", u"Охват", u"Вовлеченность", u"Тональность", u"Автор", u"Пол",
                      u"Возраст", u"Регион", u"Тип ресурса", u"Удаленные упоминания", u"Избранные упоминания"]
    headers = [u"Тема:", u"Поисковый запрос:", u"Период мониторинга:"]
    popular_mentions_table = [u"№", u"Дата", u"Время", u"Текст", u"Источник", u"Автор", u"Комментарии", u"Лайки",
                              u"Ретвитты", u"Тональность", u"Вовлеченность", u"Охват"]

    popular_mentions_small_table = [u"Доминирующий ресурс", u"Охват по доминирующему ресурсу",
                                    u"Индекс тональности популярных постов"]
    popular_mentions_comment = [u"Среди 10 самых популярных", u"От охвата по всем упоминаниям темы",
                                u"Отношение количества положительно окрашенных популярных "
                                u"постов к числу отрицательно окрашенным."]
    resource_small_table = [u"Ресурс", u"Упоминаний", u"Охват"]
    authors_table_header = [u"№", u"Автор", u"Ссылка на профиль", u"Читателей", u"Упоминаний", u"Комментариев",
                            u"Лайков", u"Ретвитов",
                            u"Положительных", u"Негативные", u"Нейтральных", u"Ресурс", u"Пол", u"Возраст", u"Регион"]
    tonal_table_header = [u"Дата", u"Время", u"Полный текст", u"Ссылка", u"Охват", u"Вовлеченность", u"Лайки",
                          u"Комментарии",
                          u"Ретвиты", u"Ник", u"Пол", u"Возраст", u"Регион", u"Ресурс", u"Тип ресурса"]
    tonal_per_day = [u"Дата", u"Нейтральные", u"Негативные", u"Положительные"]
    tonal_all = [u"Всего", u"%", u""]
    geography_table = [u"Регион", u"Упоминаний ", u"Положительных", u"Негативных", u"Нейтральных"]
    characteristics_tone_per_age = [u"Возраст", u"Упоминаний", u"Положительных", u"Негативных", u"Нейтральных"]
    characteristics_date_per_age = [u"Дата", u"до 14", u"15-18", u"19-25", u"26-30", u"31-35", u"36-45", u"46-55",
                                    u"от 55", u"Неопределено"]
    tonal_all_table_column = [u"Нейтральные", u"Негативные", u"Положительные"]
    characteristics_gen_table_column = [u"М", u"Ж", u"Неопределено"]
    characteristics_age_table_column = [u"до 14", u"15-18", u"19-25", u"26-30", u"31-35", u"36-45", u"46-55", u"от 55",
                                        u"Неопределено"]

    def __init__(self, filename, data_json):
        """
        Main constructor for template workbook object.
        :param filename:
        """
        self.workbook = Workbook(filename)

        self.title_format = self.workbook.add_format({'bold': True, 'font_size': 18, 'valign': 'vcenter'})
        self.comment_format = self.workbook.add_format({'font_size': 9, 'align': 'left',
                                                        'valign': 'vcenter', 'text_wrap': True})
        self.subtitle_format = self.workbook.add_format({'font_color': '#808080'})
        self.right_bottom_white_format = self.workbook.add_format({'right': 4, 'bottom': 4, 'text_wrap': True})
        self.right_bottom_gray_format = self.workbook.add_format({'right': 4, 'bottom': 4, 'bg_color': '#F2F2F2',
                                                                  'text_wrap': True})
        self.left_top_white_format = self.workbook.add_format({'left': 4, 'top': 4, 'text_wrap': True})
        self.left_top_gray_format = self.workbook.add_format({'left': 4, 'top': 4, 'bg_color': '#F2F2F2',
                                                              'text_wrap': True})
        self.gray_format = self.workbook.add_format({'bg_color': '#F2F2F2', 'text_wrap': True})
        self.empty_format = self.workbook.add_format()
        self.m_subtitle_format = self.workbook.add_format({'font_color': '#808080', 'text_wrap': True})
        attributes = {'bold': True, 'align': 'center', 'valign': 'vcenter', 'font_color': '#404040', 'bottom': 1,
                      'right': 4}
        self.left_part_header_format = self.workbook.add_format(attributes)
        del attributes["right"]
        attributes["left"] = 4
        self.right_part_header_format = self.workbook.add_format(attributes)
        self.link_format = self.workbook.add_format({'font_color': '#009900', 'underline': True, 'font_size': 12,
                                                     'bold': True, 'valign': 'bottom'})
        self.link_row = 6
        self.header_data = [data_json["Sh1"]["order_name"], data_json["Sh1"]["order_time"],
                            data_json["Sh1"]["order_keyword"]]
        if 'Sh11' in data_json:
            self.tag_count = len(data_json['Sh11']['tag_distr']) - 2
        else:
            self.tag_count = 0

        self.makeReviewWorksheet()  # 1-st
        self.fillReviewWorksheet(data_json['Sh2'])
        if len(data_json['Sh3']):
            self.makeMentionsWorksheet()  # 2-d
            self.fillMentionsWorksheet(data_json['Sh3'], self.tag_count)
        self.makePopularMentionsWorksheet()  # 3-d
        self.fillPopularMentionsWorksheet(data_json['Sh4'])
        self.makeResourceWorksheet()  # 4-th
        self.fillResourceWorksheet(data_json['Sh5'])
        if len(data_json['Sh6']):
            self.makeAuthorsWorksheet()  # 5-th
            self.fillAuthorsWorksheet(data_json['Sh6'])
        self.makeLeadersWorksheet()  # 6-th
        self.fillLeadersWorksheet(data_json['Sh7'])
        if data_json['Sh8']['all'][0][0] < 0.99999:
            self.makeTonalWorksheet()   # 7-th
            self.fillTonalWorksheet(data_json['Sh8'])
        self.makeGeographyWorksheet()  # 8-th
        self.fillGeographyWorksheet(data_json['Sh9'])
        self.makeCharacteristicsWorksheet()  # 9-th
        self.fillCharacteristicsWorksheet(data_json['Sh10'])
        if self.tag_count > 0:
            self.makeTagWorksheet()  # 10-th
            self.fillTagWorksheet(data_json['Sh11'])
        self.workbook.close()

    def addWorksheetHeader(self, worksheet, header_column, data_column=None):
        if not data_column:
            data_column = header_column + 1
        worksheet.hide_gridlines(option=2)
        worksheet.insert_image('B1', 'logo.png')
        separator_attributes = {'bottom': 4}
        headers_attributes = {'font_size': 10, 'valign': 'vcenter'}
        joined_format = self.workbook.add_format(dict(separator_attributes.items() + headers_attributes.items()))
        separator_format = self.workbook.add_format(separator_attributes)
        headers_format = self.workbook.add_format(headers_attributes)
        worksheet.write_column(0, header_column, self.headers[0:2], headers_format)
        for row in [0, 1]:
            worksheet.set_row(row, 19)
        worksheet.set_row(2, 19, separator_format)
        worksheet.write(2, header_column, self.headers[2], joined_format)
        data_format = list()
        data_format.append(self.workbook.add_format({'bold': True, 'align': 'left'}))
        data_format.append(self.workbook.add_format({'align': 'left'}))
        data_format.append(self.workbook.add_format({'bold': True, 'align': 'left', 'bottom': 4}))
        for i in range(0,3):
            worksheet.write(i, data_column, self.header_data[i], data_format[i])

    def addTableHeader(self, worksheet, start_row, start_col, data):
        length = len(data)
        for i, element in enumerate(data):
            if i < length - 1:
                worksheet.write(start_row, start_col + i, element, self.left_part_header_format)
            else:
                worksheet.write(start_row, start_col + i, element, self.right_part_header_format)

    def addTonalTable(self, worksheet, data):
        top_header = self.workbook.add_format({'bold': True, 'align': 'center', 'valign': 'vcenter','text_wrap': True,
                                               'font_color': '#404040'})
        for i, value in enumerate([u"Количество положительных", u"Количество негативных"]):
            worksheet.write(7, 3 + i, value, top_header)
        left_odd_header = self.workbook.add_format({'right': 4, 'bottom': 4, 'top': 4, 'valign': 'vcenter',
                                                    'align': 'right', 'bg_color': '#F2F2F2'})
        left_even_header = self.workbook.add_format({'right': 4, 'bottom': 4, 'top': 4, 'valign': 'vcenter',
                                                     'align': 'right', 'bold': True})
        for i, row in enumerate([u"Упоминания", u"Упоминания на ресурсах", u"Упоминания по авторам"]):
            worksheet.merge_range(8 + 3 * i, 1, 8 + 3 * i, 2, row, left_odd_header)
        for i, row in enumerate([u"Ресурсы", u"Авторы"]):
            worksheet.merge_range(10 + 3 * i, 1, 10 + 3 * i, 2, row, left_even_header)
        dark_green_format = self.workbook.add_format({'bottom': 4, 'top': 4, 'valign': 'vcenter', 'align': 'right',
                                                      'right': 4, 'bold': True, 'bg_color': '#C4D79B',
                                                      'font_color': '#4F6228', 'font_size': 16})
        light_green_format = self.workbook.add_format({'bottom': 4, 'top': 4, 'valign': 'vcenter', 'align': 'right',
                                                       'right': 4, 'bold': True, 'bg_color': '#EBF1DE',
                                                       'font_color': '#4F6228', 'font_size': 16})
        dark_orange_format = self.workbook.add_format({'bottom': 4, 'top': 4, 'valign': 'vcenter', 'align': 'right',
                                                      'bold': True, 'bg_color': '#FABF8F', 'font_color': '#974706',
                                                      'font_size': 16})
        light_orange_format = self.workbook.add_format({'bottom': 4, 'top': 4, 'valign': 'vcenter', 'align': 'right',
                                                        'bold': True, 'bg_color': '#FDE9D9', 'font_color': '#974706',
                                                        'font_size': 16})
        for j, row in enumerate(data):
            if j % 2:
                worksheet.write(10 + 3 * (j-1)/2,3,row[0],light_green_format)
                worksheet.write(10 + 3 * (j-1)/2,4,row[1],light_orange_format)
            else:
                worksheet.write(8 + 3*j/2,3,row[0],dark_green_format)
                worksheet.write(8 + 3*j/2,4,row[1],dark_orange_format)

    def adjustRowsAndColumns(self,worksheet, row_sizes, column_sizes):
        for col, width in column_sizes.iteritems():
            worksheet.set_column(col, col, round(width))
        for row, height in row_sizes.iteritems():
            worksheet.set_row(row, height)
    
    def formatterGenerator(self, nrow, ncol, column_formatting, start_gray_row):        
        def formatter(i, j):
            formatting = []
            if i < nrow - 1 and j < ncol - 1:
                formatting += [('right', 4), ('bottom', 4)]
            elif (i == nrow - 1 and j == 0) or (i == 0 and j == ncol - 1):
                pass
            else:
                formatting += [('left', 4), ('top', 4)]
            if start_gray_row:
                formatting += [('bg_color', '#F2F2F2'), ] if (i % 2 == 0) == (start_gray_row == 1) else []
            if column_formatting:
                formatting += column_formatting[j]
            return self.workbook.add_format(dict(formatting))
        return formatter

    def addTable(self, worksheet, start_row, start_col, data, column_formatting=None):
        if column_formatting:
            for column_index, column in enumerate(data):
                worksheet.write_column(start_row, start_col + column_index, column,
                                   self.workbook.add_format(dict(column_formatting[column_index])))
        else:
            for column_index, column in enumerate(data):
                worksheet.write_column(start_row, start_col + column_index,column)

    def addTableWithFormatting(self, worksheet, start_row, start_col, data, start_gray_row = None,
                               column_formatting = None, is_list_of_columns = True):
        if is_list_of_columns:
            ncol, nrow = len(data), len(data[0])
        else:
            nrow, ncol = len(data), len(data[0])
        formatter = self.formatterGenerator(nrow, ncol, column_formatting, start_gray_row)
        if is_list_of_columns:
            for j, column in enumerate(data):
                for i, cell in enumerate(column):
                    try:
                        worksheet.write(start_row + i, start_col + j, cell, formatter(i, j))
                    except TypeError:
                        worksheet.write(start_row + i, start_col + j, u"Fuck,why I did this!", formatter(i, j))
        else:
            for i, row in enumerate(data):
                for j, cell in enumerate(row):
                    worksheet.write(start_row + i, start_col + j, cell, formatter(i, j))

    def addInternalLink(self,worksheet_name):
        self.review.write_url(self.link_row,5,u"internal:'%s'!A1" % worksheet_name)
        self.review.write(self.link_row,5,worksheet_name,self.link_format)
        self.link_row += 1

    def makeReviewWorksheet(self):
        self.review = self.workbook.add_worksheet(u"Обзор")
        self.addWorksheetHeader(self.review, 2)
        columns = {0: 1.45, 1: 27.45, 2: 24.64, 3: 49.73, 4: 19.36, 5: 21.18, 6: 18.18}
        rows = {3: 18, 4: 22, 6: 21, 7: 21, 8: 34, 9: 21, 10: 24, 11: 21, 12: 29, 13: 21, 14: 24,
                16: 24, 37: 24, 57: 24, 59: 21}
        self.adjustRowsAndColumns(self.review, rows, columns)
        self.review.write(4, 1, u"Показатели", self.title_format)
        self.review.merge_range(4, 5, 4, 6, u'Содержание отчета', self.title_format)
        self.review.write_column(9,3,self.review_comments,self.comment_format)
        self.review.merge_range(16, 1, 16, 2, u'Динамика упоминаний по дням', self.title_format)
        self.review.write(17, 1, u"Всплески и затухания активностей по обсуждению темы в сети", self.subtitle_format)
        self.review.merge_range(37, 1, 37, 2, u'Динамика упоминаний по часам', self.title_format)
        self.review.write(38, 1, u"Активность пользователей, пишуших по теме, на протяжении суток. Рассчитана по локальному времени упоминания.", self.subtitle_format)
        self.review.write(57,1,u'Распределение упоминаний по дням',self.title_format)
        self.review.write(57,4,u'Распределение упоминаний по часам',self.title_format)
        self.addTableHeader(self.review, 59, 1, [u"Дата", u"Кол-во упоминаний"])
        self.addTableHeader(self.review, 59, 4, [u"Время", u"Кол-во упоминаний"])

    def insertChartV1(self,worksheet, datarange, categoryrange, chart_axis_title,target_cell):
        chart = self.workbook.add_chart({'type': 'line'})
        chart.add_series({
            'values': ("%s!$%s$%s:$%s$%s" % ((worksheet.name,) + datarange)),
            'categories': ("%s!$%s$%s:$%s$%s" % ((worksheet.name,) + categoryrange)),
            'line':   {'color': '#9BB51B'},
            'marker': {'type': 'circle',
                       'size': 8,
                       'border': {'color': '#2A780E'},
                       'fill':   {'color': '#9BB51B'},
                       },
            })
        chart.set_y_axis({
            'name': chart_axis_title,
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        chart.set_x_axis({
            'major_gridlines': {
                'visible': True
            }
        })
        chart.set_legend({'position': 'none'})
        chart.set_size({'width': 1060, 'height': 360})
        worksheet.insert_chart(target_cell, chart)

    def fillReviewWorksheet(self, data):
        summary_column = [data['count_posts'], data['uniq_auth'], data['post_in_day'], data['count_hosts'],
                          data['audience'], data['likes_count'], data['comments_count'], data['retweet_count'],
                          data['city_count'] + data['country_count']]
        self.addTableWithFormatting(self.review, 6, 1, [self.summary_table, summary_column],
                                    start_gray_row=2,
                                    column_formatting=[[('text_wrap', True)],
                                                       [('bold', True), ('align', 'right'),
                                                        ('font_size', 16), ('indent', 1)]])
        self.addTableWithFormatting(self.review, 60, 1, data['graph'],
                                    start_gray_row=1,
                                    column_formatting=[[('align', 'left'), ('indent', 1)]] * 2 + [[]])
        self.addTableWithFormatting(self.review, 60, 4, [self.time_distribution_column, data['time_distr']],
                                    start_gray_row=1, column_formatting=[[('align', 'left'), ('indent', 1)]] * 2)
        data_range = ("C", "61", "C", str(60 + len(data['graph'][0])))
        category_range = ("B", "61", "B", str(60 + len(data['graph'][0])))
        self.insertChartV1(self.review, data_range, category_range, u"Количество упоминаний", 'B19')
        data_range = ("F", "61", "F", "84")
        category_range = ("E", "61", "E", "84")
        self.insertChartV1(self.review, data_range, category_range, u"Упоминаний", 'B40')

    def makeMentionsWorksheet(self):
        worksheet_name = u"Упоминания"
        self.mentions = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.mentions, 3)
        rows = {4: 23.5, 5: 15}
        columns = {0: 2.18, 1: 11.64, 2: 11.45, 3: 19.09, 4: 53.09, 5: 53.09, 6: 30, 7: 6.18, 8: 6.18, 9: 6.18,
                   10: 8.45, 11: 8.45, 12: 8.45, 13: 32.18, 14: 8.45, 15: 8.45, 16: 20.36, 17: 17.73, 18: 8.45,
                   19: 8.45}
        self.adjustRowsAndColumns(self.mentions, rows, columns)
        self.mentions.merge_range(4, 1, 4, 2, worksheet_name, self.title_format)
        self.addTableHeader(self.mentions, 6, 1, self.mentions_table)

    def fillMentionsWorksheet(self, data, tag_count):
        if tag_count > 0:
            tag_table_headers = [u"Тег" + unicode(i) for i in range(1, tag_count + 1)]
            self.mentions.write_row(6, 20, tag_table_headers, self.right_part_header_format)
        mentions_column_formatting = [[('align', 'center')]] * 2 + \
                                     [[]] * 4 + \
                                     [[('align', 'center')]] * 3 + \
                                     [[]] * 4 +\
                                     [[('align', 'center')]] * 4 + \
                                     [[]] * 2 + \
                                     [[]] * tag_count
        # self.addTableWithFormatting(self.mentions, 7, 1, data, column_formatting = mentions_column_formatting)
        for index in range(3, 5):
            self.check_for_bad_content(data[index])
        self.check_for_bad_url(data[5])
        self.addTable(self.mentions, 7, 1, data, column_formatting=mentions_column_formatting)

    def makePopularMentionsWorksheet(self):
        worksheet_name = u"Популярные упоминания"
        self.popular_mentions = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.popular_mentions, 4)
        rows = {4: 23.5, 5: 35.3, 6: 20.3, 7: 23.3, 8: 48.8, 9: 30.8, 10: 23.4}

        columns = {0: 2.18, 1: 3.36, 2: 11.09, 3: 10.45, 4: 54.09, 5: 37.73, 6: 28.09, 7: 6.45, 8: 6.45, 9: 6.45, 10: 8.45,
                   11: 8.45, 12: 8.45}
        self.adjustRowsAndColumns(self.popular_mentions, rows, columns)
        self.popular_mentions.merge_range(4, 1, 4, 5, u'Популярные посты', self.title_format)
        self.popular_mentions.merge_range(5, 1, 5, 5, u'10 упоминаний с наибольшим значением вовлеученности. '
                                                      u'Вовлеченность — активность и заинтересованность аудиториии, '
                                                      u'прочитавшей пост. Рассчитывается как сумма комментариев, '
                                                      u'перепостов и отметок "Мне нравится"', self.m_subtitle_format)

        self.popular_mentions.merge_range(7, 1, 7, 3,
                                          self.popular_mentions_small_table[0],
                                          self.right_bottom_gray_format)
        self.popular_mentions.merge_range(8, 1, 8, 3,
                                          self.popular_mentions_small_table[1],
                                          self.right_bottom_white_format)
        self.popular_mentions.merge_range(9, 1, 9, 3,
                                          self.popular_mentions_small_table[2],
                                          self.gray_format)
        self.popular_mentions.write_column(7, 5,
                                           self.popular_mentions_comment,
                                           self.comment_format)
        self.addTableHeader(self.popular_mentions, 11, 1, self.popular_mentions_table)

    def check_for_bad_content(self, data_list):
        pattern = re.compile(r'([hf]t?tps?://)')
        for index, text in enumerate(data_list):
            m = pattern.search(text)
            if m:
                if m.start() == 0:
                    data_list[index] = "content: " + data_list[index]

    def check_for_bad_url(self, url_list):
        stop_rules = [
            lambda u: "onclick" in u,
            lambda u: len(re.findall(r'([hf]t?tps?://)', u)) > 1
        ]
        for index, url in enumerate(url_list):
            if any(map(
                lambda rule: rule(url),
                stop_rules
            )):
                url_list[index] = ""

    def gen_format(self, a, b):
        sum_dict = dict(a.items() + b.items())
        return self.workbook.add_format(sum_dict)

    def generate_alignment(self, short_string, percentage_position=None):
        short_dict = {'L': [[('align', 'left')]],
                      'R': [[('align', 'right')]],
                      'C': [[('align', 'center')]],
                      '_': [[]]}
        format_list = reduce(lambda x, y: x + y, map(short_dict.get, list(short_string)))
        if percentage_position:
            format_list[percentage_position].append(("num_format", 10))
        return format_list

    def fillPopularMentionsWorksheet(self, data):
        popular_mentions_column_formatting = self.generate_alignment("CCC___RRRCCC")
            # [[('align', 'center')]] * 3 + [[]] * 3 + [[('align','right')]] * 3 + [[('align', 'center')]] * 3
        self.addTableWithFormatting(self.popular_mentions, 12, 1, data['top'],
                                    column_formatting=popular_mentions_column_formatting)
        if not data["dominant_host_engage"]:
            data["dominant_host_engage"] = 0
        table_format_attributes = {'font_size': 16, 'bold': True, 'indent': 1, 'valign': 'bottom', 'align': 'right'}
        self.popular_mentions.write(7, 4, data["dominant_host"], self.gen_format({'bg_color': '#F2F2F2'},
                                                                                 table_format_attributes))
        self.popular_mentions.write(8, 4, data["dominant_host_engage"], self.gen_format({'left': 4, 'top': 4},
                                                                                        table_format_attributes))
        self.popular_mentions.write(9, 4, data["tonalIndex"],
                                    self.gen_format({'left': 4, 'top': 4, 'bg_color': '#F2F2F2'},
                                                    table_format_attributes))

    def makeResourceWorksheet(self):
        worksheet_name = u"Ресурсы"
        self.resources = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.resources, 3,5)
        rows = {3: 18.8, 4: 18.8, 5: 24, 6: 14.5, 8: 14.5, 22: 36, 23: 23.5, 39: 23.5}
        for n in range(24, 38, 1):
            rows[n] = 23.4

        columns = {0: 2.18, 1: 20.09, 2: 8.64, 3: 9.74, 4: 8.09, 5: 11.36, 6: 17.36}
        for k in range(7, 12, 1):
            columns[k] = 20.09
        self.adjustRowsAndColumns(self.resources, rows, columns)
        self.resources.merge_range(4, 1, 4, 7, u"Ресурсы с наибольшим количеством упоминаний", self.title_format)
        self.resources.merge_range(5, 1, 6, 7, u"5 ресурсов с наибольшим количеством упоминаний. "
                                               u"Охват — объем потенциальной аудитории сообщения. "
                                               u"Рассчитывается как сумма количества друзей, "
                                               u"подписчиков или последователей автора упоминания.",
                                   self.m_subtitle_format)
        self.resources.write(8, 1, self.resource_small_table[0], self.left_part_header_format)
        self.resources.merge_range(8, 2, 8, 3, self.resource_small_table[1], self.left_part_header_format)
        self.resources.merge_range(8, 4, 8, 5, self.resource_small_table[2], self.right_part_header_format)
        self.resources.merge_range(23, 1, 23, 9, u"Динамика упоминаний по популярным ресурсам", self.title_format)
        self.resources.merge_range(39, 1, 39, 9, u"Распределение упоминаний по ресурсам и дням", self.title_format)
        self.addTableHeader(self.resources, 41, 1, self.resource_small_table[0:2])

    def fillResourceWorksheet(self, data):
        top_count_header = []
        for x in data['top_count']:
            top_count_header.append(x[0])
            del x[0]
        self.addTableHeader(self.resources, 41, 5, top_count_header)
        top_count_column_format = self.generate_alignment("LCRCR_", percentage_position=2)
        self.addTableWithFormatting(self.resources, 42, 5, data['top_count'], start_gray_row=1,
                                    column_formatting=[[('align', 'center')]] * len(top_count_header))
        self.addTableWithFormatting(self.resources, 42, 1, data["count"], start_gray_row=1,
                                    column_formatting=self.generate_alignment("LC"))
        self.addTableWithFormatting(self.resources, 9, 1, data["proc"], start_gray_row=1,
                                    column_formatting=top_count_column_format)
        chart = self.workbook.add_chart({'type': 'line'})
        last_row = str(41 + len(data['top_count'][0]))
        category_range = (self.resources.name, "F", "43", "F", last_row)

        for idx, column in enumerate("GHIJK"):
            chart.add_series({
                'values': ("%s!$%s$%s:$%s$%s" % ((self.resources.name, column, "43", column, last_row))),
                'categories': ("%s!$%s$%s:$%s$%s" % category_range),
                'name': u"Ресурсы",
                'marker': {'type': 'automatic'},
            })

        chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        chart.set_x_axis({
            'major_gridlines': {
                'visible': True
            }
        })
        chart.set_size({'width': 1060, 'height': 360})
        self.resources.insert_chart("B26", chart, {'x_offset': 0, 'y_offset': 10})

        self.insertPieChart(self.resources, 'H9', [9, 3, 14, 3], [9, 1, 14, 1], {'x_offset': 25, 'y_offset': 0})

    def insertPieChart(self, worksheet, cell_to_incert, data_range, category_range, offsets, chart_size=None):
        pie_chart = self.workbook.add_chart({'type': 'pie'})
        pie_chart.add_series({
            'data_labels': {'value': True},
            'categories': [worksheet.name,] + category_range,
            'values':     [worksheet.name,] + data_range,
        })
        pie_chart.set_style(10)
        if chart_size:
            pie_chart.set_size(chart_size)
        worksheet.insert_chart(cell_to_incert, pie_chart, offsets)

    def makeAuthorsWorksheet(self):
        worksheet_name = u"Авторы"
        self.authors = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.authors, 3)
        rows = {3: 18.75, 4: 18.75, 5: 20.25, 21: 19.5, 22: 23.25, 23: 24}
        columns = {0: 2.18, 1: 4.71, 2: 21.14, 3: 30, 4: 7.29, 5: 8.71, 6: 6, 7: 6, 8: 6, 9: 6, 10: 6, 11: 7.29,
                   12: 4.71, 13: 5.71, 14: 15.71, 15: 19.71}
        self.adjustRowsAndColumns(self.authors, rows, columns)
        self.authors.merge_range(4, 1, 4, 7, u"Список авторов", self.title_format)
        self.addTableHeader(self.authors, 6, 1, self.authors_table_header)

    def fillAuthorsWorksheet(self, data):
        authors_table_formatting = self.generate_alignment("CL_CRRRRRRRLLLL")
        self.addTable(self.authors, 7, 1, data, column_formatting=authors_table_formatting)

    def makeLeadersWorksheet(self):
        worksheet_name = u"Лидеры мнений"
        self.leaders = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.leaders, 3)
        rows = {3: 18.75, 4: 26.25, 5: 30, 6: 13.5, 7: 18.75, 8: 18.75, 9: 18.75, 10: 18.75, 11: 18.75, 12: 18.75,
                13: 18.75, 14: 18.75, 15: 18.75, 16: 18.75, 17: 18.75, 18: 18.75, 19: 25.5, 20: 18.75, 21: 18.75,
                47: 30}
        columns = {0: 2.18, 1: 5, 2: 22.71, 3: 30, 4: 10.71, 5: 6, 6: 6, 7: 6, 8: 6, 9: 6.57, 10: 6.57, 11: 6.57,
                   12: 4.71, 13: 5.71, 14: 15.71, 15: 20}
        self.adjustRowsAndColumns(self.leaders, rows, columns)
        self.leaders.merge_range(4, 1, 4, 7, u"лидеры по охвату аудитории", self.title_format)
        self.leaders.merge_range(5, 1, 5, 5, u"Сообщества и персоны с наибольшей аудиторией.  Охват рассчитывается "
                                             u"как сумма количества друзей, подписчиков или последователей автора "
                                             u"упоминания.", self.subtitle_format)
        for row in [20, 65]:
            self.leaders.merge_range(row, 1, row, 7, u"Список авторов", self.title_format)
            self.addTableHeader(self.leaders,row + 2, 1, self.authors_table_header)
        self.leaders.merge_range(46, 1, 46, 7, u"Лидеры по количеству упоминаний", self.title_format)
        self.leaders.merge_range(47, 1, 47, 5, u"Сообщества и персоны с наибольшим количеством упоминаний.",
                                 self.subtitle_format)

    def insertColumnChart(self, worksheet, cell_to_incert, data_range, category_range):
        column_chart = self.workbook.add_chart({'type':'column'})
        column_chart.add_series({
            'data_labels': {'value': True,
                            'position': 'inside_end'},
            'categories': [worksheet.name, ] + category_range,
            'values':     [worksheet.name, ] + data_range,
            'fill':   {'color': '#92D050'},
        })
        column_chart.set_y_axis({
            'name': u"Охват",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        column_chart.set_size({'width': 1060, 'height': 360})
        column_chart.set_legend({'position': 'none'})
        worksheet.insert_chart(cell_to_incert,column_chart)

    def fillLeadersWorksheet(self, data):
        table_alignment = self.generate_alignment("C___CCCCCCC____")
        self.addTableWithFormatting(self.leaders, 23, 1, data["audience"],
                                    start_gray_row=1,
                                    column_formatting=table_alignment)
        self.addTableWithFormatting(self.leaders, 68, 1, data["count"],
                                    start_gray_row=1,
                                    column_formatting=table_alignment)
        table_height = 10
        if len(data["count"][0]) < 10:
            table_height = len(data["count"][0])
        chart_params = [("B8", [23, 4, 22 + table_height, 4], [23, 2, 22 + table_height, 2]),
                        ("B49", [68, 5, 67 + table_height, 5], [68, 2, 67 + table_height, 2])]
        for i in chart_params:
            self.insertColumnChart(self.leaders, i[0], i[1], i[2])

    def makeTonalWorksheet(self):
        worksheet_name = u"Тональность"
        self.tonal = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.tonal, 3)
        rows = {4: 23.25, 5: 18, 7: 33, 8: 21, 9: 21, 10: 16.5, 11: 20.25, 12: 15.75, 13: 22.5, 14: 22.5, 15: 32.25,
                16: 23.25}
        columns = {0: 2.18, 1: 9.14, 2: 13, 3: 28, 4: 28, 5: 7.57, 6: 15.57, 7: 6.57, 8: 5.71, 9: 5.57, 10: 20,
                   11: 5.57, 12: 6.43, 13: 17.43, 14: 18.71, 15: 18.43, 16: 8.14, 17: 16.14, 18: 8.14, 21: 8.43,
                   22: 8.43, 23: 8.43}
        self.adjustRowsAndColumns(self.tonal, rows, columns)
        titles = [(4, u"Тональность упоминаний"), (16, u"Динамика тональности упоминаний"),
                  (34, u"Положительные посты с наибольшим охватом"), (49, u"Негативные посты с наибольшим охватом"),
                  (63, u"Тональность упоминаний по дням")]
        for row, title in titles:
            self.tonal.merge_range(row, 1, row, 7, title, self.title_format)
        for i in [36, 51]:
            self.addTableHeader(self.tonal, i, 1, self.tonal_table_header)
        self.addTableHeader(self.tonal, 65, 1, self.tonal_per_day)
        self.addTableHeader(self.tonal, 65, 6, self.tonal_all)

    def fillTonalWorksheet(self, data):
        self.addTonalTable(self.tonal,zip(*data["top"]))
        top_posneg_table_formatting = self.generate_alignment("CCLCCCCCCLCCCCC")
        top_posneg_table_formatting[2].append(('text_wrap', True))
        for col_format in top_posneg_table_formatting:
            col_format.append(('valign', 'vcenter'))
        tonal_all_table_column_format = [[], [("num_format", 10)], []]
        self.addTableWithFormatting(self.tonal, 37, 1, data["top_positive"],
                                    start_gray_row=1,
                                    column_formatting=top_posneg_table_formatting)
        self.addTableWithFormatting(self.tonal, 52, 1, data["top_negative"],
                                    start_gray_row=1,
                                    column_formatting=top_posneg_table_formatting)
        self.addTableWithFormatting(self.tonal, 66, 1, data["dinams"], start_gray_row=1)
        self.addTableWithFormatting(self.tonal, 66, 6, [self.tonal_all_table_column] + data["all"],
                                    start_gray_row=1,
                                    column_formatting=tonal_all_table_column_format)
        self.insertPieChart(self.tonal, 'G7', [66, 7, 68, 7], [66, 6, 68, 6], {'x_offset': 25, 'y_offset': 0})

        chart = self.workbook.add_chart({'type': 'line'})
        end_row = 65 + len(data["dinams"][0])
        for column in range(2, 5):
            chart.add_series({
                'values': [self.tonal.name, 66, column, end_row, column],
                'categories': [self.tonal.name, 66, 1, end_row, 1],
                'name': self.tonal_all_table_column[column - 2],
                'marker': {'type': 'automatic'}
            })

        chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        chart.set_x_axis({
            'major_gridlines': {
                'visible': True
            }
        })
        chart.set_size({'width': 1132, 'height': 320})
        self.tonal.insert_chart("B18",chart, {'x_offset': 0, 'y_offset': 10})

    def makeGeographyWorksheet(self):
        worksheet_name = u"География упоминаний"
        self.geography = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.geography, 3,5)
        rows = {4: 23.25, 5: 23.25, 23: 23.25}
        columns = {0: 2.29, 1: 20.14, 2: 13.57, 3: 13.57, 4: 13.57, 5: 13.57}
        self.adjustRowsAndColumns(self.geography, rows, columns)
        self.geography.merge_range(4, 1, 4, 7, u"География упоминаний", self.title_format)
        self.geography.merge_range(23, 1, 23, 7, u"Самые активные регионы и распределение тональности по ним", self.title_format)
        self.addTableHeader(self.geography,39,1,self.geography_table)
        self.geography.merge_range(5,1,5,5,u"Распределение упоминаний по регионам и странам",self.subtitle_format)

    def fillGeographyWorksheet(self,data):
        self.addTableWithFormatting(self.geography, 40,1,data["loc"],start_gray_row=1)
        top_bar_chart = self.workbook.add_chart({'type': 'bar'})
        last_row = 39 + len(data["loc"][0])
        last_bar_row = last_row if last_row < 46 else 46
        top_bar_chart.add_series({
            'fill':   {'color': '#92D050'},
            'categories': [self.geography.name, 40 , 1, last_bar_row, 1],
            'values': [self.geography.name, 40, 2, last_bar_row, 2],
            'data_labels': {'value': True},
        })
        top_bar_chart.set_legend({'position': 'none'})
        top_bar_chart.set_size({'width': 1060, 'height': 310})
        top_bar_chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        self.geography.insert_chart("B7",top_bar_chart,{'x_offset': 0, 'y_offset': 10})
        activity_chart = self.createStachedColumnChart(self.geography,40,last_row)
        activity_chart.set_size({'width': 1060, 'height': 280})
        self.geography.insert_chart("B25",activity_chart,{'x_offset': 0, 'y_offset': 10})

    def createStachedColumnChart(self,worksheet, first_row, last_row,columns_range=None):
        if not columns_range:
            columns_range = range(3,6)
        activity_chart = self.workbook.add_chart({'type': 'column',  'subtype': 'stacked'})
        for idx,column in enumerate(columns_range):
            activity_chart.add_series({
                'name': self.geography_table[2 + idx],
                'categories': [worksheet.name, first_row, 1, last_row, 1],
                'values': [worksheet.name, first_row, column, last_row, column],
            })
        activity_chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        activity_chart.set_style(10)
        return activity_chart

    def makeCharacteristicsWorksheet(self):
        worksheet_name = u"Характеристики аудитории"
        self.characteristics = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        rows = {1: 16.5, 2: 18.75, 3: 18.75, 4: 18.75, 5: 18.75, 6: 18.75, 7: 18.75, 34: 21, 35: 23.25, 25: 16.5}
        columns = {0: 1.57, 1: 15, 2: 12, 3: 12.29, 4: 12.43, 5: 11, 6: 10.71, 7: 10.71, 8: 14.14, 9: 11.29, 10: 11.71, 11: 10.57}
        self.adjustRowsAndColumns(self.characteristics, rows, columns)
        self.addWorksheetHeader(self.characteristics, 3,5)
        self.characteristics.merge_range(4, 1, 4, 7, u"Характеристики аудитории", self.title_format)
        self.characteristics.merge_range(35, 1, 35, 7, u"Динамика активности возрастных групп", self.title_format)
        self.characteristics.write(5,1,u"Распределение аудитории по возрастным группам и тональность упоминаний по ним, пол аудитории",self.subtitle_format)
        self.addTableHeader(self.characteristics,24,1,self.characteristics_tone_per_age)
        self.addTableHeader(self.characteristics,58,1,self.characteristics_date_per_age)
        self.characteristics.write(25,8,u"Пол",self.left_part_header_format)
        self.characteristics.merge_range(25,9,25,10,u"Упоминаний",self.right_part_header_format)

    def fillCharacteristicsWorksheet(self,data):
        self.addTableWithFormatting(self.characteristics,25,1,[self.characteristics_age_table_column] + data['age'],
                                    start_gray_row=1,column_formatting=self.generate_alignment("LRCCC"))
        self.addTableWithFormatting(self.characteristics,26,8,[self.characteristics_gen_table_column] + data["gen"],
                                    start_gray_row=1,column_formatting=self.generate_alignment("CCC",2))
        self.addTableWithFormatting(self.characteristics,59,1,data["ages"],start_gray_row=1)
        age_tone_chart = self.createStachedColumnChart(self.characteristics, 25, 32)
        age_tone_chart.set_size({'width': 770, 'height': 290})
        age_tone_chart.set_x_axis({
            'name': u"Возраст авторов"
        })
        self.characteristics.insert_chart("B7",age_tone_chart,{'x_offset': 0, 'y_offset': 10})
        self.insertPieChart(self.characteristics, "J7", [26, 10, 28, 10],[26, 8, 28, 8],{'x_offset': 55, 'y_offset': 11})

        line_chart = self.workbook.add_chart({'type':'line'})
        end_row = 58 + len(data["ages"][0])
        for column in range(2,11):
            line_chart.add_series({
                'values': [self.characteristics.name, 59, column, end_row, column],
                'categories': [self.characteristics.name, 59, 1, end_row, 1],
                'name' : self.characteristics_date_per_age[column - 1],
                # 'marker': {'type': 'automatic'},
                })
        line_chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        line_chart.set_x_axis({
            'major_gridlines': {
                'visible': True
            }
        })
        line_chart.set_size({'width': 1110, 'height': 400})
        self.characteristics.insert_chart("B38",line_chart)


    def makeTagWorksheet(self):
        worksheet_name = u"Теги"
        self.tags = self.workbook.add_worksheet(worksheet_name)
        self.addInternalLink(worksheet_name)
        self.addWorksheetHeader(self.tags, 3, 5)
        rows = {4: 23.25, 5: 16.5, 49: 23.25, 30: 23.25}
        columns = {0: 2.29, 1: 20.29, 2: 9.14, 3: 8.29, 4: 15.29, 5: 11, 6: 13.14, 7: 9.14, 8: 14.14}
        self.adjustRowsAndColumns(self.tags, rows, columns)
        self.tags.write(5,1,u"Количество упоминаний по тегам и распределение тональности внутри тега, а также индекс тональности.",self.subtitle_format)
        titles = [(4,u"Распределение упоминаний по тегам"),(30,u"Распределение тональности по тегам"),(49,u"Динамика упоминаний по тегам")]
        for row, title in titles:
            self.tags.merge_range(row, 1, row, 7, title, self.title_format)
        self.tags.write(7,1,u"Теги",self.left_part_header_format)
        self.tags.merge_range(7,2,7,3,u"Упоминаний",self.left_part_header_format)
        self.addTableHeader(self.tags,7,4,[u"Положительных",u"Негативных",u"Нейтральных"])

    def fillTagWorksheet(self,data):
        self.addTableWithFormatting(self.tags,71,1,data["tag_distr"],start_gray_row=2)
        self.addTableWithFormatting(self.tags,8,1,data["tag_counts"],start_gray_row=1,column_formatting=self.generate_alignment("CCRCCC", 2))
        last_row = 7 + len(data["tag_counts"][0])
        self.insertPieChart(self.tags, "I7", [8, 2, last_row, 2],[8, 1, last_row, 1], {'x_offset': 20, 'y_offset': 11}, chart_size={'width': 520, 'height': 460})
        tone_tag_chart = self.createStachedColumnChart(self.tags,8,last_row,columns_range=range(4,7))
        tone_tag_chart.set_size({'width':1170,'height':320})
        self.tags.insert_chart("B33",tone_tag_chart)

        line_chart = self.workbook.add_chart({'type':'line'})
        end_row = 70 + len(data["tag_distr"][0])
        num_columns = len(data["tag_distr"]) - 1
        for column in range(2,2 + num_columns):
            line_chart.add_series({
                'values': [self.tags.name, 72, column, end_row, column],
                'categories': [self.tags.name, 72, 1, end_row, 1],
                'name' : data["tag_distr"][column - 1][0],
                'marker': {'type': 'automatic'},
                })
        line_chart.set_y_axis({
            'name': u"Количество упоминаний",
            'name_font': {
                'bold': True,
                'font_size': 10
            }
        })
        line_chart.set_x_axis({
            'major_gridlines': {
                'visible': True
            }
        })
        line_chart.set_size({'width': 1170, 'height': 388})
        self.tags.insert_chart("B52",line_chart)