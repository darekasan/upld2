from websocket_server import WebsocketServer
import logging
import threading
import json
import time

#self.server.send_message_to_all("hey all, a new client has joined us")
#self.server.send_message(client, msg)

# message implements
# ["message_kind":"なんちゃら", ...]
# clients_list サーバー→クライアント
# set_name クライアント→サーバー
# text id content

class SyncPlayerServer():
    clients = {}
    log = []

    player_filenum = '1'
    player_time = 0.0
    player_start_time = 0.0
    player_is_playing = False

    def __init__(self, host, port):
        self.server = WebsocketServer(port, host=host, loglevel=logging.DEBUG)

    # on new client
    def new_client(self, client, server):
        self.clients[client['id']]={'name':'id'+str(client['id']), 'is_ready':False}
        self.send_client_list()
        self.send_text('[SERVER]', self.clients[client['id']]['name'] + 'が参加しました')

        obj = { \
            'message_kind': 'player_load', \
            'filenum': self.player_filenum}
        self.server.send_message(client, json.dumps(obj))

    # on client left
    def client_left(self, client, server):
        print("client({}) disconnected".format(client['id']))
        self.server.clients.remove(client)
        if(len(self.server.clients)>0):
            self.send_client_list()
            self.send_text('[SERVER]', self.clients[client['id']]['name'] + 'が退出しました')
        self.clients.pop(client['id'])

    # on message received
    def message_received(self, client, server, message_raw):
        print("client({}) said: {}".format(client['id'], message_raw))

        message = json.loads(message_raw)

        # クライアントの名前を設定する
        if message['message_kind'] in 'set_name' :
            self.send_text('[SERVER]', '名前変更 ' + self.clients[client['id']]['name'] + ' → ' + message['name'])
            self.clients[client['id']]['name'] = message['name']
            self.send_client_list()
        elif message['message_kind'] in 'text' :
            self.send_text(self.clients[client['id']]['name'], message['content'])

        # ファイルを読み込む
        elif message['message_kind'] in 'player_load' :
            self.player_filenum = message['filenum']
            self.player_time = 0.0
            self.player_start_time = 0.0
            self.player_is_playing = False

            obj = { \
                'message_kind': 'player_load', \
                'filenum': self.player_filenum}
            self.send_message_exclude_a_client(client, json.dumps(obj))
            self.send_text('[SERVER]', self.clients[client['id']]['name'] + 'が' + self.player_filenum + 'をロードしました')

            for key in self.clients :
                self.clients[key]['is_ready']=False
            self.send_client_list()

        # 再生したりポーズしたり
        elif message['message_kind'] in 'player_play_pause' :
            self.player_time = message['time']
            self.player_start_time = time.time()
            self.player_is_playing = message['is_playing']

            obj = { \
                'message_kind': 'player_play_pause', \
                'time': self.player_time,\
                'is_playing': self.player_is_playing}
            self.send_message_exclude_a_client(client, json.dumps(obj))
            if self.player_is_playing:
                self.send_text('[SERVER]', self.clients[client['id']]['name'] + 'によって再生')
            else:
                self.send_text('[SERVER]', self.clients[client['id']]['name'] + 'によって一時停止')


        # 時間を取得（遅れて来た人向け）
        # elif message['message_kind'] in 'get_player_time' :
        #     if self.player_is_playing:
        #         diff = time.time() - self.player_start_time
        #         ptime = self.player_time + diff
        #     else:
        #         ptime = self.player_time
        #
        #     obj = { \
        #         'message_kind': 'player_time', \
        #         'time': ptime, \
        #         'is_playing': self.player_is_playing}
        #     self.server.send_message(client,json.dumps(obj))

        # 準備完了
        elif message['message_kind'] in 'set_ready' :
            self.clients[client['id']]['is_ready'] = message['is_ready']

            if self.player_is_playing:
                diff = time.time() - self.player_start_time
                ptime = self.player_time + diff
            else:
                ptime = self.player_time

            obj = { \
                'message_kind': 'player_play_pause', \
                'time': ptime, \
                'is_playing': self.player_is_playing}
            self.server.send_message(client, json.dumps(obj))

            # obj = { \
            #     'message_kind': 'set_ready', \
            #     'id' : client['id'],\
            #     'is_ready': self.clients[client['id']]['is_ready']}
            # self.server.send_message_to_all(json.dumps(obj))
            self.send_client_list()

    # クライアント一覧を送信
    def send_client_list(self):
        self.server.send_message_to_all(json.dumps({'message_kind': 'clients_list', 'clients': self.clients}))

    # テキストメッセージを送信すると同時にログに貯める
    def send_text(self, name, content):
        obj = { \
            'message_kind': 'text', \
            'name': name, \
            'content': content}
        self.server.send_message_to_all(json.dumps(obj))
        self.log.append(obj)

    # あるクライアント以外に送信
    def send_message_exclude_a_client(self, client, message):
        for item in self.server.clients:
            if item == client :
                continue
            self.server.send_message(item, message)

    # run server
    def run(self):
        self.server.set_fn_new_client(self.new_client)
        self.server.set_fn_client_left(self.client_left)
        self.server.set_fn_message_received(self.message_received)
        self.server.run_forever()
        
IP_ADDR = "0.0.0.0"
PORT=9001
server = SyncPlayerServer(IP_ADDR, PORT)
server.run()