
import sys
from template import Template
import web
import zlib
import json

urls = ('/upload', 'Upload')
past = []

def create_path_from_hash(hash):
    return "/var/www/project/xlsxexportpy/data/" + hash + ".xlsx"
class Upload:
    def POST(self):
        data = web.data()
        decompressed_data = zlib.decompress(data, 16+zlib.MAX_WBITS)
        data_json = json.loads(decompressed_data)
        export_hash = data_json['Sh1']['hash']
        if export_hash in past:
            return web.ok
        else:
            past.append(export_hash)
        with open("data/{0}.json".format(export_hash),"w+") as fd:
            fd.write(json.dumps(data_json, indent=4))
        path_to_file = create_path_from_hash(export_hash)
        Template(
            path_to_file,
            data_json
        )
        print("creating export with hash {0}".format(export_hash))
        return web.ok


if __name__ == '__main__':
    if len(sys.argv)>1:
        if sys.argv[1]=='test':
            with open("data/bba9be314f67e2efebb544ac89ce61d6.json","r") as fd:
                dj = json.loads(fd.read())
            Template("test.xlsx",dj)
    else:
        app = web.application(urls, globals())
        app.run()