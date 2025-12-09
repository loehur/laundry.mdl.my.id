
  function Print(id, btn) {
    function __startBtnLoading(b) {
      try {
        if (!b) return;
        if (b.dataset.loading === '1') return;
        b.dataset.loading = '1';
        b.dataset.prevHtml = b.innerHTML;
        b.classList.add('disabled');
        b.style.pointerEvents = 'none';
        b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      } catch (e) {}
    }

    function __endBtnLoading(b) {
      try {
        if (!b) return;
        b.classList.remove('disabled');
        b.style.pointerEvents = '';
        if (b.dataset.prevHtml) {
          b.innerHTML = b.dataset.prevHtml;
          b.dataset.prevHtml = '';
        }
        b.dataset.loading = '';
      } catch (e) {}
    }
    __startBtnLoading(btn);

    var el = document.getElementById("print" + id);
    var rows = el.querySelectorAll('tr');
    var lines = [];
    for (var i = 0; i < rows.length; i++) {
      var tr = rows[i];
      var tds = tr.querySelectorAll('td');
      if (tr.id && tr.id.toLowerCase() === 'dashrow') {
        lines.push(makeDash(width));
        continue;
      }
      if (tds.length === 0) {
        continue;
      }
      var width = parseInt(localStorage.getItem('escpos_width') || '32');
      if (!width || isNaN(width)) {
        width = 32;
      }
      var escLine = function(left, right, width) {
        var token = /\[\[(?:\/)?B\]\]/g;
        var rawL = (left || '').replace(/[ \t]+/g, ' ').trim();
        var rawR = (right || '').replace(/[ \t]+/g, ' ').trim();
        var plainL = rawL.replace(token, '');
        var plainR = rawR.replace(token, '');
        var space = width - plainL.length - plainR.length;
        if (space < 1) space = 1;
        return rawL + Array(space + 1).join(' ') + rawR;
      };
      var makeDash = function(w) {
        return Array(w + 1).join('-');
      };
      var cellToLines = function(td) {
        var html = td.innerHTML || '';
        var s = html.replace(/<br\s*\/?>/gi, '\n');
        s = s.replace(/&nbsp;/gi, '.');
        s = s.replace(/\u00a0/g, '.');
        s = s.replace(/<b>/gi, '[[B]]').replace(/<\/b>/gi, '[[/B]]');
        s = s.replace(/<[^>]+>/g, '');
        s = s.replace(/\r\n/g, '\n');
        var arr = s.split('\n');
        var out = [];
        for (var a = 0; a < arr.length; a++) {
          var t = arr[a].replace(/[ \t]+/g, ' ').trim();
          if (t.length > 0) {
            out.push(t);
          }
        }
        return out;
      };
      if (tds.length === 1 || (tds[0].getAttribute('colspan') === '2')) {
        var arr1 = cellToLines(tds[0]);
        for (var x = 0; x < arr1.length; x++) {
          lines.push(arr1[x]);
        }
      } else if (tds.length >= 2) {
        var arrL = cellToLines(tds[0]);
        var arrR = cellToLines(tds[1]);
        var max = Math.max(arrL.length, arrR.length);
        for (var y = 0; y < max; y++) {
          var left = arrL[y] || '';
          var right = arrR[y] || '';
          lines.push(escLine(left, right, width));
        }
      }
    }

    var encoder = new TextEncoder();
    var chunks = [];
    chunks.push(new Uint8Array([27, 64]));
    var esc_font = (localStorage.getItem('escpos_font') || 'A').toUpperCase();
    var esc_cp = parseInt(localStorage.getItem('escpos_codepage') || '16');
    var esc_line = parseInt(localStorage.getItem('escpos_line') || '36');
    var esc_size = (localStorage.getItem('escpos_size') || 'normal').toLowerCase();
    var sizeVal = 0;
    if (esc_size === 'doublew') sizeVal = 1;
    if (esc_size === 'doubleh') sizeVal = 16;
    if (esc_size === 'doublehw') sizeVal = 17;
    chunks.push(new Uint8Array([27, 77, esc_font === 'A' ? 0 : 1]));
    chunks.push(new Uint8Array([27, 116, isNaN(esc_cp) ? 16 : esc_cp]));
    chunks.push(new Uint8Array([27, 51, isNaN(esc_line) ? 24 : esc_line]));
    chunks.push(new Uint8Array([29, 33, sizeVal]));
    var addLine = function(s, align) {
      chunks.push(new Uint8Array([27, 97, align]));
      var re = /\[\[B\]\]|\[\[\/B\]\]/g;
      var pos = 0;
      var bold = false;
      var m;
      while ((m = re.exec(s)) !== null) {
        var t = s.substring(pos, m.index);
        if (t.length > 0) {
          chunks.push(new Uint8Array([27, 69, bold ? 1 : 0]));
          chunks.push(encoder.encode(t));
        }
        bold = (m[0] === '[[B]]');
        pos = m.index + m[0].length;
      }
      var tail = s.substring(pos);
      if (tail.length > 0) {
        chunks.push(new Uint8Array([27, 69, bold ? 1 : 0]));
        chunks.push(encoder.encode(tail));
      }
      chunks.push(new Uint8Array([27, 69, 0]));
      chunks.push(encoder.encode("\n"));
    };
    for (var j = 0; j < lines.length; j++) {
      if (j < 2) {
        addLine(lines[j], 1);
      } else if (j === 2) {
        addLine("--------------------------------", 0);
        addLine(lines[j], 0);
      } else {
        addLine(lines[j], 0);
      }
    }
    chunks.push(encoder.encode("\n\n\n"));
    chunks.push(new Uint8Array([29, 86, 66, 0]));
    var totalLen = 0;
    for (var k = 0; k < chunks.length; k++) totalLen += chunks[k].length;
    var all = new Uint8Array(totalLen);
    var offset = 0;
    for (var m = 0; m < chunks.length; m++) {
      all.set(chunks[m], offset);
      offset += chunks[m].length;
    }

    function fallbackHtml() {
      var divContents = el.innerHTML;
      var a = window.open('');
      a.document.write('<title>Print Page</title>');
      a.document.write('<body style="margin-left: <?= $this->mdl_setting['print_ms'] ?>mm">');
      a.document.write(divContents);
      var window_width = $(window).width();
      a.print();
      if (window_width > 600) {
        a.close()
      } else {
        setTimeout(function() {
          a.close()
        }, 60000);
      }
      loadDiv();
      __endBtnLoading(btn);
    }

    function tryBluetooth() {
      if (!navigator.bluetooth) {
        fallbackHtml();
        return;
      }

      function doWrite(characteristic, data) {
        var size = 20;
        var idx = 0;
        var p = Promise.resolve();
        while (idx < data.length) {
          var chunk = data.slice(idx, Math.min(idx + size, data.length));
          p = p.then(function(c) {
            return characteristic.writeValue(c);
          }.bind(null, chunk));
          idx += size;
        }
        return p;
      }
      navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: ['0000ffe0-0000-1000-8000-00805f9b34fb', '0000ff00-0000-1000-8000-00805f9b34fb']
      }).then(function(device) {
        return device.gatt.connect();
      }).then(function(server) {
        return server.getPrimaryService('0000ffe0-0000-1000-8000-00805f9b34fb').catch(function() {
          return server.getPrimaryService('0000ff00-0000-1000-8000-00805f9b34fb');
        });
      }).then(function(service) {
        return service.getCharacteristic('0000ffe1-0000-1000-8000-00805f9b34fb').catch(function() {
          return service.getCharacteristic('0000ff01-0000-1000-8000-00805f9b34fb');
        });
      }).then(function(characteristic) {
        return doWrite(characteristic, all);
      }).then(function() {
        loadDiv();
        __endBtnLoading(btn);
      }).catch(function() {
        fallbackHtml();
      });
    }

    function escposGetSavedBaud() {
      var b = parseInt(localStorage.getItem('escpos_baud') || '9600');
      if (!b || isNaN(b)) b = 9600;
      return b;
    }

    function escposGetSavedPort() {
      return navigator.serial.getPorts().then(function(ports) {
        if (!ports || ports.length === 0) {
          return null;
        }
        var vid = parseInt(localStorage.getItem('escpos_vendor') || '0');
        var pid = parseInt(localStorage.getItem('escpos_product') || '0');
        if (vid && pid) {
          for (var i = 0; i < ports.length; i++) {
            var info = ports[i].getInfo ? ports[i].getInfo() : {};
            if (info && info.usbVendorId === vid && info.usbProductId === pid) {
              return ports[i];
            }
          }
        }
        return ports[0];
      });
    }

    function escposSavePort(port, baud) {
      try {
        var info = port.getInfo ? port.getInfo() : {};
        if (info && info.usbVendorId) localStorage.setItem('escpos_vendor', String(info.usbVendorId));
        if (info && info.usbProductId) localStorage.setItem('escpos_product', String(info.usbProductId));
        localStorage.setItem('escpos_baud', String(baud));
      } catch (e) {}
    }

    function trySerial() {
      if (!navigator.serial) {
        tryBluetooth();
        return;
      }
      if (!window.__escpos) {
        window.__escpos = {
          port: null,
          writer: null,
          open: false,
          baud: 9600
        };
      }
      var start = function() {
        var size = 256;
        var idx = 0;
        var process = Promise.resolve();
        var w = window.__escpos.writer;
        while (idx < all.length) {
          var chunk = all.slice(idx, Math.min(idx + size, all.length));
          process = process.then(function(c) {
            return w.write(c);
          }.bind(null, chunk));
          idx += size;
        }
        return process.then(function() {
          loadDiv();
          __endBtnLoading(btn);
        }).catch(function() {
          tryBluetooth();
        });
      };
      if (window.__escpos.open && window.__escpos.writer) {
        start();
        return;
      }
      var openWithSettings = function(rate) {
        return window.__escpos.port.open({
            baudRate: rate,
            dataBits: 8,
            stopBits: 1,
            parity: 'none',
            flowControl: 'none'
          })
          .then(function() {
            if (window.__escpos.port.setSignals) {
              return window.__escpos.port.setSignals({
                dataTerminalReady: true,
                requestToSend: true
              });
            }
          });
      };
      escposGetSavedPort()
        .then(function(saved) {
          if (saved) {
            window.__escpos.port = saved;
            var b = escposGetSavedBaud();
            return openWithSettings(b).catch(function() {
              return openWithSettings(9600);
            });
          }
          return navigator.serial.requestPort().then(function(p) {
            window.__escpos.port = p;
            return openWithSettings(9600).catch(function() {
              return openWithSettings(115200);
            });
          });
        })
        .then(function() {
          window.__escpos.writer = window.__escpos.port.writable.getWriter();
          window.__escpos.open = true;
          try {
            escposSavePort(window.__escpos.port, escposGetSavedBaud());
          } catch (e) {}
          return start();
        })
        .catch(function() {
          tryBluetooth();
        });
    }

    trySerial();
  }