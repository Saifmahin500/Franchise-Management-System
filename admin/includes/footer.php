    <!-- ========================== -->
    <!-- JS Scripts -->
    <!-- ========================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Branch-wise Profit Chart
        const ctx1 = document.getElementById('branchProfitChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= $branchNames ?>,
                datasets: [{
                    label: 'Profit',
                    data: <?= $branchProfits ?>,
                    backgroundColor: '#ffdd99',
                    borderColor: '#173831',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Monthly Sales vs Expenses Chart
        const months = <?= json_encode($months) ?>;
        const sales = <?= json_encode($salesData) ?>;
        const expenses = <?= json_encode($expenseData) ?>;

        const ctx2 = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                        label: 'Sales',
                        data: sales,
                        borderColor: 'green',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        borderColor: 'red',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>