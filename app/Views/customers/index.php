<div class="card card-soft">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Customer Management</h5>
            <button class="btn btn-primary">+ New Customer</button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Loyalty Points</th>
                        <th>Membership</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= htmlspecialchars($customer->fullName) ?></td>
                            <td><?= htmlspecialchars($customer->phone) ?></td>
                            <td><?= htmlspecialchars($customer->email) ?></td>
                            <td><?= (int) $customer->loyaltyPoints ?></td>
                            <td><?= htmlspecialchars(ucfirst($customer->membershipType)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
