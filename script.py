#!/usr/bin/python
import os
import threading
import subprocess
import datetime


def compile(exe, src, dest):
    stdbuf_cmd = ['stdbuf', '-oL']
    cmd = ['php', exe, src, dest]
    proc = subprocess.Popen(stdbuf_cmd + cmd, stdout=subprocess.PIPE)
    while proc.poll() is None:
        for line in proc.stdout:
            line = str(line, 'UTF-8').rstrip('\n')
            print(line)


def launch():
    path: tuple = (
        ('sass/foo.scss', 'sass/compiled/foo.css'),
        ('sass/bar.scss', 'sass/compiled/bar.css'),
    )
    i: int = 0
    thr: list = []
    for i in range(len(path)):
        x = "threading.Thread(target=compile, args=('{}', '{}', '{}'))"
        x = x.format(ext_php, path[i][0], path[i][1])
        thr.append(x)

        eval(thr[i]).start()
        # eval(thr[i]).join()

        i += 1


def kill():
    #  今の状態では threading の終了を待たないのでそのまま終了するとエラーを吐く
    #  Ctrl-C 時に適切な方法でPHPのループとPythonのループを終わらせるコードを書く
    pass


def main():
    print(str(datetime.datetime.today().isoformat()) + ": " + "Started watching")

    #  カレントディレクトリの変更
    cd: str = os.getcwd()
    os.chdir(cd)


    if not os.path.exists(ext_php):
        print('WARNING: This script cannot be run without another script file!')
        exit(1)

    launch()


if __name__ == "__main__":
    ext_php: str = './core.php'
    main()
