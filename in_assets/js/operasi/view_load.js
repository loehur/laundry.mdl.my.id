// View Load JavaScript - Laundry Management System
// Separated from view_load.php for better maintainability

(function () {
  "use strict";

  // Global variables
  window.noref = "";
  window.json_rekap = [];
  window.totalBill = 0;
  window.idNya = 0;
  window.diBayar = 0;
  window.idtargetOperasi = 0;
  window.totalNotif = "";
  var klikNotif = 0;
  var userClick = "";
  var click = 0;

  // Inisialisasi konfigurasi dari window.ViewLoadConfig (akan diset dari PHP)
  var config = window.ViewLoadConfig || {};
  var BASE_URL = config.baseUrl || "";
  var modeView = config.modeView || "0";
  var id_pelanggan = config.idPelanggan || "";
  var print_mode = config.printMode || "bluetooth";
  var print_ms = config.printMs || "0";

  $(document).ready(function () {
    clearTuntas();
    $("tr#nTunaiBill").hide();
    $("select.tize").selectize();
    window.totalBill = $("span#totalBill").attr("data-total");
    if (config.loadRekap) {
      window.json_rekap = [config.loadRekap];
    }
    try {
      var sumRekap = 0;
      var lr = config.loadRekap || {};
      for (var k in lr) {
        if (!Object.prototype.hasOwnProperty.call(lr, k)) continue;
        var v = parseInt(lr[k] || 0);
        if (!isNaN(v)) sumRekap += v;
      }
      if (sumRekap <= 0) {
        $("#btnModalLoadRekap").addClass("d-none");
      }
    } catch (e) {}
  });

  $(".hoverBill").hover(
    function () {
      $(this).addClass("bg-light");
    },
    function () {
      $(this).removeClass("bg-light");
    }
  );

  $("span.nonTunaiMetod").click(function () {
    $("input[name=noteBayar]").val($(this).html());
    $("input[name=noteBill]").val($(this).html());
  });

  function clearTuntas() {
    if (config.arrTuntas && config.arrTuntas.length > 0) {
      $.ajax({
        url: BASE_URL + "Antrian/clearTuntas",
        data: {
          data: config.arrTuntasSerial,
        },
        type: "POST",
        success: function (response) {
          loadDiv();
        },
      });
    }
  }

  $("form.ajax").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr("action"),
      data: $(this).serialize(),
      type: $(this).attr("method"),
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function (res) {
        if (res == 0) {
          try {
            var mEl =
              document.querySelector(".modal.show") ||
              document.getElementById("modalLoadRekap");
            if (mEl && window.bootstrap && bootstrap.Modal) {
              var instance =
                bootstrap.Modal.getInstance(mEl) || new bootstrap.Modal(mEl);
              instance.hide();
            }
          } catch (e) {}
          try {
            $(".modal-backdrop").remove();
            $("body")
              .removeClass("modal-open")
              .css({ overflow: "", paddingRight: "" });
          } catch (e) {}
          hide_modal();
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  });

  $("form.ajax_json").on("submit", function (e) {
    e.preventDefault();

    var karyawanBill = $("#karyawanBill").val();
    var metodeBill = $("#metodeBill").val();
    var noteBill = $("#noteBill").val();

    noteBill = noteBill.replace(" ", "_SPACE_");

    $.ajax({
      url:
        BASE_URL +
        "Operasi/bayarMulti/" +
        karyawanBill +
        "/" +
        id_pelanggan +
        "/" +
        metodeBill +
        "/" +
        noteBill,
      data: {
        rekap: window.json_rekap,
        dibayar: $("input#bayarBill").val(),
      },
      type: $(this).attr("method"),
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function (res) {
        if (res == 0) {
          try {
            var mEl = document.getElementById("modalLoadRekap");
            if (mEl && window.bootstrap && bootstrap.Modal) {
              var instance =
                bootstrap.Modal.getInstance(mEl) || new bootstrap.Modal(mEl);
              instance.hide();
            }
          } catch (e) {}
          try {
            $(".modal-backdrop").remove();
            $("body")
              .removeClass("modal-open")
              .css({ overflow: "", paddingRight: "" });
          } catch (e) {}
          if (typeof hide_modal === "function") {
            try {
              hide_modal();
            } catch (e) {}
          }
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  });

  $("span.addOperasi").on("click", function (e) {
    e.preventDefault();
    $("div.letakRAK").hide();
    $("input#letakRAK").prop("required", false);

    window.idNya = $(this).attr("data-id");
    var valueNya = $(this).attr("data-value");
    var layanan = $(this).attr("data-layanan");
    $("input.idItem").val(window.idNya);
    $("input.valueItem").val(valueNya);
    $("b.operasi").html(layanan);
    window.idtargetOperasi = $(this).attr("id");

    var ref_ini = $(this).attr("data-ref");
    var totalNotif = $("span#textTotal" + ref_ini).html();
    $("input[name=inTotalNotif]").val(totalNotif);

    var textNya = $("span.selesai" + window.idNya).html();
    var hpNya = $("span.selesai" + window.idNya).attr("data-hp");
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);
  });

  $("span.gantiOperasi").on("click", function (e) {
    e.preventDefault();
    window.idNya = $(this).attr("data-id");
    var awal = $(this).attr("data-awal");
    $("input#id_ganti").val(window.idNya);
    $("span#awalOP").html(awal);
  });

  $("span.endLayanan").on("click", function (e) {
    e.preventDefault();
    $("div.letakRAK").show();
    $("input#letakRAK").prop("required", true);
    $("form.operasi").attr("data-operasi", "operasiSelesai");
    window.idNya = $(this).attr("data-id");
    var valueNya = $(this).attr("data-value");
    var layanan = $(this).attr("data-layanan");
    window.noref = $(this).attr("data-ref");
    $("input.idItem").val(window.idNya);
    $("input.valueItem").val(valueNya);
    $("b.operasi").html(layanan);
    window.idtargetOperasi = $(this).attr("id");

    var textNya = $("span.selesai" + window.idNya).html();
    var hpNya = $("span.selesai" + window.idNya).attr("data-hp");
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);

    var ref_ini = $(this).attr("data-ref");
    var totalNotif = $("span#textTotal" + ref_ini).html();
    $("input[name=inTotalNotif]").val(totalNotif);
  });

  $(".tambahCas").click(function () {
    window.noref = $(this).attr("data-ref");
    window.idNya = $(this).attr("data-tr");
    $("#" + window.idNya).val(window.noref);
  });

  $("a.hapusRef").on("dblclick", function (e) {
    e.preventDefault();
    var refNya = $(this).attr("data-ref");
    var note = prompt("Alasan Hapus:", "");
    if (note === null || note.length == 0) {
      return;
    }
    $.ajax({
      url: BASE_URL + "Antrian/hapusRef",
      data: {
        ref: refNya,
        note: note,
      },
      type: "POST",
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function (response) {
        loadDiv();
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  });

  $("a.hapusRef").on("click", function (e) {
    e.preventDefault();
  });

  $("a.ambil").on("click", function (e) {
    e.preventDefault();
    window.idNya = $(this).attr("data-id");
    $("input.idItem").val(window.idNya);
  });

  $("a.sendNotif").on("click", function (e) {
    klikNotif += 1;
    if (klikNotif > 1) {
      return;
    }
    $(this).fadeOut("slow");
    e.preventDefault();
    var urutRef = $(this).attr("data-urutRef");
    var id_pelanggan_notif = $(this).attr("data-idPelanggan");
    var id_harga = $(this).attr("data-id_harga");
    var hpNya = $(this).attr("data-hp");
    var refNya = $(this).attr("data-ref");
    var timeNya = $(this).attr("data-time");
    var textNya = $("span#" + urutRef).html();
    var countMember = $("span#member" + urutRef).html();
    $.ajax({
      url: BASE_URL + "Antrian/sendNotif/" + countMember + "/1",
      data: {
        hp: hpNya,
        text: textNya,
        id_harga: id_harga,
        ref: refNya,
        time: timeNya,
        idPelanggan: id_pelanggan_notif,
      },
      type: "POST",
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function (res) {
        if (res == 0) {
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  });

  $("a.sendNotifMember").on("click", function (e) {
    klikNotif += 1;
    if (klikNotif > 1) {
      return;
    }
    $(this).fadeOut("slow");
    e.preventDefault();
    var refNya = $(this).attr("data-ref");
    $.ajax({
      url: BASE_URL + "Member/sendNotifDeposit/" + refNya,
      data: {},
      type: "POST",
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function () {
        loadDiv();
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  });

  $("a.bayarPasMulti").on("click", function (e) {
    $("input#bayarBill").val(window.totalBill);
    bayarBill();
  });

  $("select.metodeBayarBill").on("keyup change", function () {
    if ($(this).val() == 2) {
      $("tr#nTunaiBill").show();
    } else {
      $("tr#nTunaiBill").hide();
    }
  });

  $("select.userChange").change(function () {
    userClick = $("select.userChange option:selected").text();
  });

  $("span.editRak").on("click", function () {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var ref_ini = $(this).attr("data-ref");
    var totalNotif = $("span#textTotal" + ref_ini).html();

    var id_value = $(this).attr("data-id");
    var value = $(this).attr("data-value");
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html(
      "<input type='text' maxLength='2' id='value_' style='text-align:center;width:30px' value='" +
        value.toUpperCase() +
        "'>"
    );

    $("#value_").focus();

    console.log(totalNotif);

    $("#value_").focusout(function () {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: BASE_URL + "Antrian/updateRak/",
          data: {
            id: id_value,
            value: value_after,
            totalNotif: totalNotif,
          },
          type: "POST",
          beforeSend: function () {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function () {
            span.html(value_after.toUpperCase());
            span.attr("data-value", value_after.toUpperCase());
            click = 0;
          },
          complete: function () {
            $(".loaderDiv").fadeOut("slow");
          },
        });
      }
    });
  });

  $("span.editPack").on("click", function () {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr("data-id");
    var value = $(this).attr("data-value");
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html(
      "<input type='number' min='0' id='value_' style='text-align:center;width:45px' value='" +
        value +
        "'>"
    );

    $("#value_").focus();
    $("#value_").focusout(function () {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: BASE_URL + "Antrian/updateRak/1",
          data: {
            id: id_value,
            value: value_after,
          },
          type: "POST",
          beforeSend: function () {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function () {
            loadDiv();
          },
          complete: function () {
            $(".loaderDiv").fadeOut("slow");
          },
        });
      }
    });
  });

  $("span.editHanger").on("click", function () {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr("data-id");
    var value = $(this).attr("data-value");
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html(
      "<input type='number' min='0' id='value_' style='text-align:center;width:45px' value='" +
        value +
        "'>"
    );

    $("#value_").focus();
    $("#value_").focusout(function () {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: BASE_URL + "Antrian/updateRak/2",
          data: {
            id: id_value,
            value: value_after,
          },
          type: "POST",
          beforeSend: function () {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function () {
            loadDiv();
          },
          complete: function () {
            $(".loaderDiv").fadeOut("slow");
          },
        });
      }
    });
  });

  window.PrintContentRef = function (id, idPelanggan) {
    var countMember = $("span#member" + id).html();
    if (countMember > 0) {
      $.ajax({
        url: BASE_URL + "Member/textSaldo",
        data: {
          id: idPelanggan,
        },
        type: "POST",
        success: function (result) {
          $("td.textMember" + id).html(result);
          if (window.requestAnimationFrame) {
            requestAnimationFrame(function () {
              requestAnimationFrame(function () {
                Print(id);
              });
            });
          } else {
            setTimeout(function () {
              Print(id);
            }, 0);
          }
        },
      });
    } else {
      Print(id);
    }
  };

  $("input#bayarBill").on("keyup change", function () {
    bayarBill();
  });

  function bayarBill() {
    var dibayar = parseInt($("input#bayarBill").val());
    var kembalian = parseInt(dibayar) - parseInt(window.totalBill);
    if (kembalian > 0) {
      $("input#kembalianBill").val(kembalian);
    } else {
      $("input#kembalianBill").val(0);
    }
  }

  window.totalBill = $("span#totalBill").attr("data-total");

  $("input.cek").change(function () {
    var jumlah = $(this).attr("data-jumlah");
    let refRekap = $(this).attr("data-ref");

    if ($(this).is(":checked")) {
      window.totalBill = parseInt(window.totalBill) + parseInt(jumlah);
      window.json_rekap[0][refRekap] = jumlah;
    } else {
      delete window.json_rekap[0][refRekap];
      window.totalBill = parseInt(window.totalBill) - parseInt(jumlah);
    }

    $("span#totalBill")
      .html(window.totalBill.toLocaleString("en-US"))
      .attr("data-total", window.totalBill);
    bayarBill();
  });

  window.Print = function (id, btn) {
    function __startBtnLoading(b) {
      try {
        if (!b) return;
        if (b.dataset.loading === "1") return;
        b.dataset.loading = "1";
        b.dataset.prevHtml = b.innerHTML;
        b.classList.add("disabled");
        b.style.pointerEvents = "none";
        b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      } catch (e) {}
    }

    function __endBtnLoading(b) {
      try {
        if (!b) return;
        b.classList.remove("disabled");
        b.style.pointerEvents = "";
        if (b.dataset.prevHtml) {
          b.innerHTML = b.dataset.prevHtml;
          b.dataset.prevHtml = "";
        }
        b.dataset.loading = "";
      } catch (e) {}
    }

    if (!btn) {
      try {
        var candidates = document.querySelectorAll(
          "a[onclick],button[onclick],span[onclick]"
        );
        var re = new RegExp("Print\\(\\s*(\"|\\')?" + id + "(\"|\\')?");
        for (var ci = 0; ci < candidates.length; ci++) {
          var oc = candidates[ci].getAttribute("onclick") || "";
          if (re.test(oc)) {
            btn = candidates[ci];
            break;
          }
        }
      } catch (e) {}
    }

    if (window.__printLockUntil && Date.now() < window.__printLockUntil) {
      return;
    }
    window.__printLockUntil = Date.now() + 3000;

    try {
      var __icons = document.querySelectorAll("i.fas.fa-print");
      for (var ii = 0; ii < __icons.length; ii++) {
        var el = __icons[ii];
        el.dataset.printSwap = "1";
        el.classList.remove("fa-print");
        el.classList.add("fa-spinner", "fa-spin");
      }
      setTimeout(function () {
        var sp = document.querySelectorAll("i.fas.fa-spinner.fa-spin");
        for (var si = 0; si < sp.length; si++) {
          var el2 = sp[si];
          if (el2.dataset.printSwap === "1") {
            el2.classList.remove("fa-spinner", "fa-spin");
            el2.classList.add("fa-print");
            el2.dataset.printSwap = "";
          }
        }
        window.__printLockUntil = 0;
      }, 3000);
    } catch (e) {}

    var el = document.getElementById("print" + id);
    var pmode = print_mode;
    pmode = (pmode || "bluetooth").toLowerCase();
    var rows = el.querySelectorAll("tr");
    var lines = [];

    for (var i = 0; i < rows.length; i++) {
      var tr = rows[i];
      var tds = tr.querySelectorAll("td");
      if (tr.id && tr.id.toLowerCase() === "dashrow") {
        lines.push(makeDash(width));
        continue;
      }
      if (tds.length === 0) {
        continue;
      }
      var width = parseInt(localStorage.getItem("escpos_width") || "32");
      if (!width || isNaN(width)) {
        width = 32;
      }

      var escLine = function (left, right, width) {
        var token = /\[\[(?:\/)?(?:B|DEL|H1|C|R|L|TD)\]\]/g;
        var rawL = (left || "").replace(/[ \t]+/g, " ").trim();
        var rawR = (right || "").replace(/[ \t]+/g, " ").trim();
        var plainL = rawL.replace(token, "");
        var plainR = rawR.replace(token, "");
        if (pmode === "server") {
          var out = "";
          if (plainL.length > 0) out += "[[TD]]" + rawL + "[[/TD]]";
          if (plainR.length > 0) out += "[[TD]]" + rawR + "[[/TD]]";
          return out;
        }
        var space = width - plainL.length - plainR.length;
        if (space < 1) space = 1;
        return rawL + Array(space + 1).join(" ") + rawR;
      };

      var makeDash = function (w) {
        return Array(w + 1).join("-");
      };

      var cellToLines = function (td) {
        var html = td.innerHTML || "";
        var s = html;
        if (pmode !== "server") {
          s = s.replace(/<br\s*\/?>/gi, "\n");
        }
        s = s.replace(/&nbsp;/gi, " ");
        s = s.replace(/\u00a0/g, " ");
        s = s.replace(/<b>/gi, "[[B]]").replace(/<\/b>/gi, "[[/B]]");
        s = s.replace(/<h1>/gi, "[[H1]]").replace(/<\/h1>/gi, "[[/H1]]");
        s = s.replace(/<del>/gi, "[[DEL]]").replace(/<\/del>/gi, "[[/DEL]]");
        if (pmode === "server") {
          s = s.replace(/<(?!br\b)[^>]+>/gi, "");
        } else {
          s = s.replace(/<[^>]+>/g, "");
        }
        s = s.replace(/\r\n/g, "\n");
        var arr = s.split("\n");
        var out = [];
        for (var a = 0; a < arr.length; a++) {
          var raw = arr[a];
          var t = raw.replace(/[ \t]+/g, " ").trim();
          if (t.length > 0) {
            out.push(t);
          } else if (pmode === "server") {
            out.push("");
          }
        }
        return out;
      };

      var getAlign = function (td) {
        try {
          var ta =
            td.style && td.style.textAlign
              ? td.style.textAlign.toLowerCase()
              : "";
          if (!ta && window.getComputedStyle) {
            ta = window.getComputedStyle(td).textAlign.toLowerCase();
          }
          return ta || "left";
        } catch (e) {
          return "left";
        }
      };

      var sanitizeServerTd = function (td) {
        try {
          var s = td.innerHTML || "";
          s = s.replace(/&nbsp;/gi, " ");
          s = s.replace(/\u00a0/g, " ");
          s = s.replace(/<(?!br\b)[^>]+>/gi, "");
          s = s.replace(/[\r\n]+/g, " ");
          s = s.replace(/[ \t]+/g, " ").trim();
          return s;
        } catch (e) {
          return "";
        }
      };

      if (tds.length === 1 || tds[0].getAttribute("colspan") === "2") {
        if (pmode === "server") {
          var v = sanitizeServerTd(tds[0]);
          v = "[[TD]]" + v + "[[/TD]]";
          lines.push("[[TR]]" + v + "[[/TR]]");
        } else {
          var a0 = getAlign(tds[0]);
          var arr1 = cellToLines(tds[0]);
          for (var x = 0; x < arr1.length; x++) {
            var v2 = arr1[x];
            if (a0 === "center") v2 = "[[C]]" + v2 + "[[/C]]";
            else if (a0 === "right") v2 = "[[R]]" + v2 + "[[/R]]";
            else v2 = "[[L]]" + v2 + "[[/L]]";
            lines.push(v2);
          }
        }
      } else if (tds.length >= 2) {
        if (pmode === "server") {
          var left0 = sanitizeServerTd(tds[0]);
          var right0 = sanitizeServerTd(tds[1]);
          var row0 = escLine(left0, right0, width);
          lines.push("[[TR]]" + row0 + "[[/TR]]");
        } else {
          var arrL = cellToLines(tds[0]);
          var arrR = cellToLines(tds[1]);
          var aL = getAlign(tds[0]);
          var aR = getAlign(tds[1]);
          var max = Math.max(arrL.length, arrR.length);
          for (var y = 0; y < max; y++) {
            var left = arrL[y] || "";
            var right = arrR[y] || "";
            if (aL === "center") left = "[[C]]" + left + "[[/C]]";
            else if (aL === "right") left = "[[R]]" + left + "[[/R]]";
            else left = "[[L]]" + left + "[[/L]]";
            if (aR === "center") right = "[[C]]" + right + "[[/C]]";
            else if (aR === "right") right = "[[R]]" + right + "[[/R]]";
            else right = "[[L]]" + right + "[[/L]]";
            lines.push(escLine(left, right, width));
          }
        }
      }
    }

    var encoder = new TextEncoder();
    var chunks = [];
    chunks.push(new Uint8Array([27, 64]));
    var esc_font = (localStorage.getItem("escpos_font") || "A").toUpperCase();
    var esc_cp = parseInt(localStorage.getItem("escpos_codepage") || "16");
    var esc_line = parseInt(localStorage.getItem("escpos_line") || "36");
    var esc_size = (
      localStorage.getItem("escpos_size") || "normal"
    ).toLowerCase();
    var sizeVal = 0;
    if (esc_size === "doublew") sizeVal = 1;
    if (esc_size === "doubleh") sizeVal = 16;
    if (esc_size === "doublehw") sizeVal = 17;
    chunks.push(new Uint8Array([27, 77, esc_font === "A" ? 0 : 1]));
    chunks.push(new Uint8Array([27, 116, isNaN(esc_cp) ? 16 : esc_cp]));
    chunks.push(new Uint8Array([27, 51, isNaN(esc_line) ? 24 : esc_line]));
    chunks.push(new Uint8Array([29, 33, sizeVal]));

    var addLine = function (s, align) {
      s = s || "";
      var center = false;
      if (s.indexOf("[[C]]") === 0) {
        center = true;
        s = s.substring(5);
      }
      s = s.replace(/\[\[(?:\/)?(?:B|DEL|H1|C|R|L|TD)\]\]/g, "");
      chunks.push(new Uint8Array([27, 97, center ? 1 : align]));
      chunks.push(encoder.encode(s));
      chunks.push(encoder.encode("\n"));
    };

    for (var j = 0; j < lines.length; j++) {
      if (j < 2) {
        addLine(lines[j], 1);
      } else {
        addLine(lines[j], 0);
      }
    }

    chunks.push(encoder.encode("\n\n\n"));
    var doCut = (localStorage.getItem("escpos_cut") || "0") === "1";
    if (doCut) {
      chunks.push(new Uint8Array([29, 86, 0]));
    }

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
      var a = window.open("");
      a.document.write("<title>Print Page</title>");
      a.document.write('<body style="margin-left: ' + print_ms + 'mm">');
      a.document.write(divContents);
      var window_width = $(window).width();
      a.print();
      if (window_width > 600) {
        a.close();
      } else {
        setTimeout(function () {
          a.close();
        }, 60000);
      }
      loadDiv();
    }

    function tryBluetooth() {
      if (!navigator.bluetooth) {
        console.log("Bluetooth API tidak tersedia");
        return;
      }

      function doWrite(characteristic, data) {
        var size = 20;
        var idx = 0;
        var p = Promise.resolve();
        while (idx < data.length) {
          var chunk = data.slice(idx, Math.min(idx + size, data.length));
          p = p.then(
            function (c) {
              return characteristic.writeValue(c);
            }.bind(null, chunk)
          );
          idx += size;
        }
        return p;
      }

      navigator.bluetooth
        .requestDevice({
          acceptAllDevices: true,
          optionalServices: [
            "0000ffe0-0000-1000-8000-00805f9b34fb",
            "0000ff00-0000-1000-8000-00805f9b34fb",
          ],
        })
        .then(function (device) {
          return device.gatt.connect();
        })
        .then(function (server) {
          return server
            .getPrimaryService("0000ffe0-0000-1000-8000-00805f9b34fb")
            .catch(function () {
              return server.getPrimaryService(
                "0000ff00-0000-1000-8000-00805f9b34fb"
              );
            });
        })
        .then(function (service) {
          return service
            .getCharacteristic("0000ffe1-0000-1000-8000-00805f9b34fb")
            .catch(function () {
              return service.getCharacteristic(
                "0000ff01-0000-1000-8000-00805f9b34fb"
              );
            });
        })
        .then(function (characteristic) {
          return doWrite(characteristic, all);
        })
        .then(function () {
          loadDiv();
        })
        .catch(function (err) {
          console.log("BLE write error:", err);
        });
    }

    function escposGetSavedBaud() {
      var b = parseInt(localStorage.getItem("escpos_baud") || "9600");
      if (!b || isNaN(b)) b = 9600;
      return b;
    }

    function escposGetSavedPort() {
      return navigator.serial.getPorts().then(function (ports) {
        if (!ports || ports.length === 0) {
          return null;
        }
        var vid = parseInt(localStorage.getItem("escpos_vendor") || "0");
        var pid = parseInt(localStorage.getItem("escpos_product") || "0");
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
        if (info && info.usbVendorId)
          localStorage.setItem("escpos_vendor", String(info.usbVendorId));
        if (info && info.usbProductId)
          localStorage.setItem("escpos_product", String(info.usbProductId));
        localStorage.setItem("escpos_baud", String(baud));
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
          baud: 9600,
        };
      }

      var openWithSettings = function (rate) {
        return window.__escpos.port
          .open({
            baudRate: rate,
            dataBits: 8,
            stopBits: 1,
            parity: "none",
            flowControl: "none",
          })
          .then(function () {
            if (window.__escpos.port.setSignals) {
              return window.__escpos.port.setSignals({
                dataTerminalReady: true,
                requestToSend: true,
              });
            }
          });
      };

      escposGetSavedPort()
        .then(function (saved) {
          if (saved) {
            window.__escpos.port = saved;
            var b = escposGetSavedBaud();
            return openWithSettings(b).catch(function () {
              return openWithSettings(9600);
            });
          }
          var vid = parseInt(localStorage.getItem("escpos_vendor") || "0");
          var pid = parseInt(localStorage.getItem("escpos_product") || "0");
          var opts = {};
          if (vid && pid) {
            opts = {
              filters: [
                {
                  usbVendorId: vid,
                  usbProductId: pid,
                },
              ],
            };
          }
          return navigator.serial.requestPort(opts).then(function (p) {
            window.__escpos.port = p;
            return openWithSettings(9600).catch(function () {
              return openWithSettings(115200);
            });
          });
        })
        .then(function () {
          var size = 256,
            idx = 0,
            p = Promise.resolve();
          var writer = window.__escpos.port.writable.getWriter();
          window.__escpos.open = true;
          try {
            escposSavePort(window.__escpos.port, escposGetSavedBaud());
          } catch (e) {}
          while (idx < all.length) {
            var chunk = all.slice(idx, Math.min(idx + size, all.length));
            p = p.then(
              function (c) {
                return writer.write(c);
              }.bind(null, chunk)
            );
            idx += size;
          }
          return p.then(function () {
            writer.releaseLock();
            loadDiv();
          });
        })
        .catch(function () {
          tryBluetooth();
        });
    }

    if (pmode === "bluetooth") {
      console.log("Metode cetak: bluetooth");
      tryBluetooth();
    } else if (pmode === "esc/pos" || pmode === "escpos" || pmode === "esc") {
      console.log("Metode cetak: esc/pos (serial)");
      trySerial();
    } else if (pmode === "server") {
      console.log("Metode cetak: server");
      try {
        if (pmode === "server") {
          lines = lines.filter(function (s) {
            var x = String(s || "");
            if (x.indexOf("[[TR]]") === -1) return true;
            var inner = x.replace(/\[\[(?:\/)?(?:TR|TD)\]\]/g, "");
            return inner.trim().length > 0;
          });
        }
        var plain =
          lines
            .map(function (s) {
              s = String(s || "");
              s = s.replace(/\[\[B\]\]/g, "<b>");
              s = s.replace(/\[\[\/B\]\]/g, "</b>");
              s = s.replace(/\[\[H1\]\]/g, "<h1>");
              s = s.replace(/\[\[\/H1\]\]/g, "</h1>");
              s = s.replace(/\[\[(?:\/)?C\]\]/g, "");
              s = s.replace(/\[\[(?:\/)?R\]\]/g, "");
              s = s.replace(/\[\[(?:\/)?L\]\]/g, "");
              s = s.replace(/\[\[TD\]\]/g, "<td>");
              s = s.replace(/\[\[\/TD\]\]/g, "</td>");
              s = s.replace(/\[\[TR\]\]/g, "<tr>");
              s = s.replace(/\[\[\/TR\]\]/g, "</tr>");
              s = s.replace(/\[\[(?:\/)?DEL\]\]/g, "");
              return s;
            })
            .join(pmode === "server" ? "" : "\n") +
          (pmode === "server" ? "" : "\n");
        try {
          console.log(
            "Server print payload (length=" + plain.length + "):\n",
            plain
          );
        } catch (e) {}
        fetch("http://localhost:3000/print", {
          method: "POST",
          headers: {
            "Content-Type": "text/plain",
          },
          body: plain,
        })
          .then(function (res) {
            console.log("Server print status:", res.status);
            return res.text().catch(function () {
              return "";
            });
          })
          .then(function (body) {
            console.log("Server print body:", body);
            loadDiv();
          })
          .catch(function (err) {
            console.log("Server print error:", err);
          });
      } catch (e) {
        console.log("Server print exception:", e);
      }
    } else {
      console.log("Metode cetak: default (bluetooth)");
      tryBluetooth();
    }
  };

  window.cekQris = function (ref_id, jumlah) {
    $.ajax({
      url: BASE_URL + "Kas/cek_qris/" + ref_id + "/" + jumlah,
      data: {},
      type: "POST",
      beforeSend: function () {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function (res) {
        if (res == 0) {
          loadDiv();
        }
      },
      complete: function () {
        $(".loaderDiv").fadeOut("slow");
      },
    });
  };

  function loadDiv() {
    if (modeView != 2) {
      var pelanggan = $("select[name=pelanggan").val();
      $("div#load").load(BASE_URL + "Operasi/loadData/" + pelanggan + "/0");
    }
    if (modeView == 2) {
      var pelanggan = $("select[name=pelanggan").val();
      var tahun = $("select[name=tahun").val();
      $("div#load").load(
        BASE_URL + "Operasi/loadData/" + pelanggan + "/" + tahun
      );
    }
  }

  window.PrintQR = function (data, text, btn) {
    var t = String(data || "");
    var label = String(text || "");

    function __startBtnLoading(b) {
      try {
        if (!b) return;
        if (b.dataset.loading === "1") return;
        b.dataset.loading = "1";
        b.dataset.prevHtml = b.innerHTML;
        b.classList.add("disabled");
        b.style.pointerEvents = "none";
        b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      } catch (e) {}
    }

    function __endBtnLoading(b) {
      try {
        if (!b) return;
        b.classList.remove("disabled");
        b.style.pointerEvents = "";
        if (b.dataset.prevHtml) {
          b.innerHTML = b.dataset.prevHtml;
          b.dataset.prevHtml = "";
        }
        b.dataset.loading = "";
      } catch (e) {}
    }

    if (window.__printLockUntil && Date.now() < window.__printLockUntil) {
      return;
    }
    window.__printLockUntil = Date.now() + 3000;

    try {
      var __icons = document.querySelectorAll("i.fas.fa-print");
      for (var ii = 0; ii < __icons.length; ii++) {
        var el = __icons[ii];
        el.dataset.printSwap = "1";
        el.classList.remove("fa-print");
        el.classList.add("fa-spinner", "fa-spin");
      }
      setTimeout(function () {
        var sp = document.querySelectorAll("i.fas.fa-spinner.fa-spin");
        for (var si = 0; si < sp.length; si++) {
          var el2 = sp[si];
          if (el2.dataset.printSwap === "1") {
            el2.classList.remove("fa-spinner", "fa-spin");
            el2.classList.add("fa-print");
            el2.dataset.printSwap = "";
          }
        }
        window.__printLockUntil = 0;
      }, 3000);
    } catch (e) {}

    var encoder = new TextEncoder();
    var chunks = [];
    chunks.push(new Uint8Array([27, 64]));
    chunks.push(new Uint8Array([27, 97, 1]));
    chunks.push(new Uint8Array([29, 40, 107, 4, 0, 49, 65, 49, 0]));
    chunks.push(new Uint8Array([29, 40, 107, 3, 0, 49, 67, 5]));
    chunks.push(new Uint8Array([29, 40, 107, 3, 0, 49, 69, 48]));
    var db = encoder.encode(t);
    var len = db.length + 3;
    var pL = len & 255;
    var pH = (len >> 8) & 255;
    chunks.push(new Uint8Array([29, 40, 107, pL, pH, 49, 80, 48]));
    chunks.push(db);
    chunks.push(new Uint8Array([29, 40, 107, 3, 0, 49, 81, 48]));
    chunks.push(encoder.encode("\n"));
    if (label.length > 0) {
      chunks.push(new Uint8Array([27, 97, 1]));
      chunks.push(encoder.encode(label));
      chunks.push(encoder.encode("\n"));
    }
    chunks.push(encoder.encode("\n"));
    var qrFeed = parseInt(localStorage.getItem("escpos_qr_feed") || "6");
    chunks.push(new Uint8Array([27, 100, isNaN(qrFeed) ? 6 : qrFeed]));
    chunks.push(new Uint8Array([27, 97, 0]));
    var doCutQr = (localStorage.getItem("escpos_cut") || "0") === "1";
    if (doCutQr) {
      chunks.push(new Uint8Array([29, 86, 0]));
    }

    var total = 0;
    for (var i = 0; i < chunks.length; i++) total += chunks[i].length;
    var all = new Uint8Array(total);
    var off = 0;
    for (var j = 0; j < chunks.length; j++) {
      all.set(chunks[j], off);
      off += chunks[j].length;
    }

    var pmode = print_mode;
    pmode = (pmode || "bluetooth").toLowerCase();

    function tryBluetooth() {
      if (!navigator.bluetooth) {
        return;
      }

      function w(ch, d) {
        var s = 20,
          idx = 0,
          p = Promise.resolve();
        while (idx < d.length) {
          var c = d.slice(idx, Math.min(idx + s, d.length));
          p = p.then(
            function (x) {
              return ch.writeValue(x);
            }.bind(null, c)
          );
          idx += s;
        }
        return p;
      }

      navigator.bluetooth
        .requestDevice({
          acceptAllDevices: true,
          optionalServices: [
            "0000ffe0-0000-1000-8000-00805f9b34fb",
            "0000ff00-0000-1000-8000-00805f9b34fb",
          ],
        })
        .then(function (dev) {
          return dev.gatt.connect();
        })
        .then(function (srv) {
          return srv
            .getPrimaryService("0000ffe0-0000-1000-8000-00805f9b34fb")
            .catch(function () {
              return srv.getPrimaryService(
                "0000ff00-0000-1000-8000-00805f9b34fb"
              );
            });
        })
        .then(function (svc) {
          return svc
            .getCharacteristic("0000ffe1-0000-1000-8000-00805f9b34fb")
            .catch(function () {
              return svc.getCharacteristic(
                "0000ff01-0000-1000-8000-00805f9b34fb"
              );
            });
        })
        .then(function (ch) {
          return w(ch, all);
        });
    }

    function escposGetSavedBaud() {
      var b = parseInt(localStorage.getItem("escpos_baud") || "9600");
      if (!b || isNaN(b)) b = 9600;
      return b;
    }

    function escposGetSavedPort() {
      return navigator.serial.getPorts().then(function (ports) {
        if (!ports || ports.length === 0) {
          return null;
        }
        var vid = parseInt(localStorage.getItem("escpos_vendor") || "0");
        var pid = parseInt(localStorage.getItem("escpos_product") || "0");
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
        if (info && info.usbVendorId)
          localStorage.setItem("escpos_vendor", String(info.usbVendorId));
        if (info && info.usbProductId)
          localStorage.setItem("escpos_product", String(info.usbProductId));
        localStorage.setItem("escpos_baud", String(baud));
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
          open: false,
          baud: escposGetSavedBaud(),
        };
      }
      var port = window.__escpos.port;

      var openWith = function (rate) {
        return port
          .open({
            baudRate: rate,
            dataBits: 8,
            stopBits: 1,
            parity: "none",
            flowControl: "none",
          })
          .then(function () {
            if (port.setSignals)
              return port.setSignals({
                dataTerminalReady: true,
                requestToSend: true,
              });
          });
      };

      var writeAll = function () {
        var writer = port.writable.getWriter();
        var size = 256,
          idx = 0,
          p = Promise.resolve();
        while (idx < all.length) {
          var chunk = all.slice(idx, Math.min(idx + size, all.length));
          p = p.then(
            function (c) {
              return writer.write(c);
            }.bind(null, chunk)
          );
          idx += size;
        }
        return p.then(function () {
          writer.releaseLock();
        });
      };

      var startSerial = function () {
        writeAll()
          .then(function () {
            window.__escpos.open = true;
          })
          .catch(function () {
            tryBluetooth();
          });
      };

      if (port && window.__escpos.open) {
        startSerial();
        return;
      }
      if (port && !window.__escpos.open) {
        openWith(window.__escpos.baud)
          .catch(function () {
            return openWith(9600);
          })
          .then(function () {
            startSerial();
          });
        return;
      }

      escposGetSavedPort()
        .then(function (saved) {
          if (saved) {
            port = saved;
            window.__escpos.port = port;
            return openWith(window.__escpos.baud).catch(function () {
              return openWith(9600);
            });
          }
          return navigator.serial.requestPort().then(function (p) {
            port = p;
            window.__escpos.port = port;
            return openWith(9600).catch(function () {
              return openWith(115200);
            });
          });
        })
        .then(function () {
          try {
            escposSavePort(port, window.__escpos.baud);
          } catch (e) {}
          startSerial();
        })
        .catch(function () {
          tryBluetooth();
        });
    }

    if (pmode === "bluetooth") {
      console.log("Metode cetak QR: bluetooth");
      tryBluetooth();
    } else if (pmode === "esc/pos" || pmode === "escpos" || pmode === "esc") {
      console.log("Metode cetak QR: esc/pos (serial)");
      trySerial();
    } else if (pmode === "server") {
      console.log("Metode cetak QR: server");
      try {
        fetch("http://localhost:3000/printqr", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            qr_string: t,
            text: label,
          }),
        })
          .then(function (res) {
            console.log("Server printqr status:", res.status);
            return res.text().catch(function () {
              return "";
            });
          })
          .then(function (body) {
            console.log("Server printqr body:", body);
          })
          .catch(function (err) {
            console.log("Server printqr error:", err);
          });
      } catch (e) {
        console.log("Server printqr exception:", e);
      }
    } else {
      console.log("Metode cetak QR: default (bluetooth)");
      tryBluetooth();
    }
  };
})();
