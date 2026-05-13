'use strict';
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    floatchart();
  }, 500);
});

function floatchart() {
  (function () {
    // Lấy dữ liệu từ attribute Blade
    const weeklyEl = document.querySelector('#visitor-chart');
    const monthlyEl = document.querySelector('#visitor-chart-1');

    const weeklyData = JSON.parse(weeklyEl.dataset.weekVisitors || '{}');
    const monthlyData = JSON.parse(monthlyEl.dataset.monthVisitors || '{}');

    // Weekly Chart
    const weeklyOptions = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2', '#ff4d4f'],
      series: [
        { name: 'Lượt xem trang', data: weeklyData.pageViews },
        { name: 'Lượt truy cập', data: weeklyData.sessions },
        { name: 'Người dùng', data: weeklyData.users }
      ],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: {
        categories: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật']
      },
      tooltip: {
        y: {
          formatter: (val) => val.toLocaleString('vi-VN')
        }
      }
    };
    new ApexCharts(weeklyEl, weeklyOptions).render();

    // Monthly Chart
    const monthlyOptions = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2', '#ff4d4f'],
      series: [
        { name: 'Lượt xem trang', data: monthlyData.pageViews },
        { name: 'Lượt truy cập', data: monthlyData.sessions },
        { name: 'Người dùng', data: monthlyData.users }
      ],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: {
        categories: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']
      },
      tooltip: {
        y: {
          formatter: (val) => val.toLocaleString('vi-VN')
        }
      }
    };
    new ApexCharts(monthlyEl, monthlyOptions).render();
  })();


  (function () {
    // tuần
    const chartEl = document.querySelector('#income-chart-week');
    if (!chartEl) return;

    const weeklyIncome = JSON.parse(chartEl.dataset.weeklyIncome || '[]');

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    };

    var options = {
      chart: {
        type: 'bar',
        height: 380,
        toolbar: {
          show: false
        }
      },
      colors: ['#13c2c2'],
      plotOptions: {
        bar: {
          columnWidth: '45%',
          borderRadius: 4
        }
      },
      dataLabels: {
        enabled: false
      },
      series: [{
        name: "Doanh thu",
        data: weeklyIncome
      }],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      xaxis: {
        categories: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      tooltip: {
        y: {
          formatter: formatCurrency
        }
      },
      grid: {
        show: false
      }
    };
    var chart = new ApexCharts(chartEl, options);
    chart.render();

    // tháng
    const monthEl = document.querySelector('#income-chart-month');
    if (!monthEl) return;

    const monthlyIncome = JSON.parse(monthEl.dataset.monthlyIncome || '[]');
    const monthLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];

    const monthOptions = {
      chart: {
        type: 'bar',
        height: 380,
        toolbar: { show: false }
      },
      colors: ['#722ed1'],
      plotOptions: {
        bar: {
          columnWidth: '45%',
          borderRadius: 4
        }
      },
      dataLabels: { enabled: false },
      series: [{
        name: "Doanh thu",
        data: monthlyIncome
      }],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      xaxis: {
        categories: monthLabels,
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: { show: false },
      tooltip: {
        y: {
          formatter: (val) => new Intl.NumberFormat('vi-VN').format(val) + ' đ'
        }
      },
      grid: { show: false }
    };

    new ApexCharts(monthEl, monthOptions).render();
  })();

  (function () {
    const el = document.querySelector('#analytics-report-chart');
    const labels = JSON.parse(el.dataset.categoryLabels || '[]');
    const data = JSON.parse(el.dataset.categoryData || '[]');

    const options = {
      chart: {
        type: 'bar',
        height: 375,
        toolbar: { show: false }
      },
      colors: ['#faad14'],
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: false,
          columnWidth: '40px',
        }
      },
      dataLabels: {
        enabled: true,
        formatter: val => val.toLocaleString('vi-VN')
      },
      series: [{
        name: 'Sản phẩm đã bán',
        data: data
      }],
      xaxis: {
        categories: labels,
        labels: {
          style: { fontSize: '13px' }
        }
      },
      yaxis: {
        labels: {
          formatter: val => val.toLocaleString('vi-VN')
        }
      },
      tooltip: {
        y: {
          formatter: val => val.toLocaleString('vi-VN') + ' sản phẩm'
        }
      }
    };

    new ApexCharts(el, options).render();
  })();


  (function () {
    // === Biểu đồ tháng ===
    var monthlySold = JSON.parse(document.getElementById("sold-chart-month").dataset.monthlySold || "null");
    var monthlyByYear = JSON.parse(document.getElementById("sold-chart-month").dataset.monthlyByYear || "null");

    var monthOptions;
    if (monthlySold) {
      // Chế độ 1 năm
      monthOptions = {
        chart: { type: "area", height: 350 },
        series: [{ name: "Sản phẩm đã bán", data: monthlySold }],
        xaxis: { categories: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"] },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: val => val + " Sản phẩm" } },
        yaxis: {
          labels: {
            formatter: function (val) {
              return parseInt(val); // bỏ .0
            }
          }
        }
      };
    } else if (monthlyByYear) {
      // Chế độ tất cả các năm
      var seriesData = [];
      for (var year in monthlyByYear) {
        seriesData.push({ name: year, data: monthlyByYear[year] });
      }
      monthOptions = {
        chart: { type: "area", height: 350 },
        series: seriesData,
        xaxis: { categories: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"] },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: val => val + " Sản phẩm" } },
        yaxis: {
          labels: {
            formatter: function (val) {
              return parseInt(val);
            }
          }
        }
      };
    }

    if (monthOptions) {
      new ApexCharts(document.querySelector('#sold-chart-month'), monthOptions).render();
    }

    // === Biểu đồ năm ===
    var yearlySoldData = JSON.parse(document.getElementById("sold-chart-year").dataset.yearlySold || "{}");
    var years = Object.keys(yearlySoldData);
    var totals = Object.values(yearlySoldData);

    var yearOptions = {
      chart: { type: "bar", height: 350 },
      plotOptions: {
        bar: {
          columnWidth: '40%',
          borderRadius: 4
        }
      },

      series: [{ name: "Sản phẩm đã bán", data: totals }],
      xaxis: { categories: years },
      tooltip: { y: { formatter: val => val + " Sản phẩm" } },
      yaxis: {
        labels: {
          formatter: function (val) {
            return parseInt(val);
          }
        }
      }
    };

    new ApexCharts(document.querySelector('#sold-chart-year'), yearOptions).render();

    // === Sự kiện đổi năm ===
    document.getElementById("yearFilter").addEventListener("change", function () {
      document.getElementById("yearFilterForm").submit();
    });

  })();

  (function () {
    var options = {
      chart: {
        type: 'bar',
        height: 430,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          columnWidth: '30%',
          borderRadius: 4
        }
      },
      stroke: {
        show: true,
        width: 8,
        colors: ['transparent']
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        show: true,
        fontFamily: `'Public Sans', sans-serif`,
        offsetX: 10,
        offsetY: 10,
        labels: {
          useSeriesColors: false
        },
        markers: {
          width: 10,
          height: 10,
          radius: '50%',
          offsexX: 2,
          offsexY: 2
        },
        itemMargin: {
          horizontal: 15,
          vertical: 5
        }
      },
      colors: ['#faad14', '#1890ff'],
      series: [{
        name: 'Net Profit',
        data: [180, 90, 135, 114, 120, 145]
      }, {
        name: 'Revenue',
        data: [120, 45, 78, 150, 168, 99]
      }],
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
      },
    }
    var chart = new ApexCharts(document.querySelector('#sales-report-chart'), options);
    chart.render();
  })();
}
