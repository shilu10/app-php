<?= loadPartial("header") ?>
<?= loadPartial("navbar") ?>

<?php
// Check if we are editing an existing job
$isEdit = isset($job) && isset($job['id']);

// Form action: create goes to /jobs, edit goes to /jobs/{id}/edit
$formAction = $isEdit 
    ? "/jobs/" . urlencode($job['id']) . "/edit" 
    : "/jobs";
?>

<section class="flex justify-center items-center mt-20">
  <div class="bg-white p-8 rounded-lg shadow-md w-full md:w-600 mx-6">
    <h2 class="text-4xl text-center font-bold mb-4">
      <?= $isEdit ? "Edit Job Listing" : "Create Job Listing" ?>
    </h2>

    <!-- Add ID so JavaScript can find it -->
    <form id="jobForm" method="POST" action="<?= $formAction ?>">
      <?php if ($isEdit): ?>
        <!-- This hidden input allows method spoofing if needed in backend -->
        <input type="hidden" name="_method" value="PATCH">
      <?php endif; ?>

      <!-- Job Info Section -->
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-500">
        Job Info
      </h2>

      <div class="mb-4">
        <input type="text" name="title" placeholder="Job Title"
          value="<?= htmlspecialchars($job['title'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" required />
      </div>

      <div class="mb-4">
        <textarea name="description" placeholder="Job Description"
          class="w-full px-4 py-2 border rounded focus:outline-none"><?= htmlspecialchars($job['description'] ?? '') ?></textarea>
      </div>

      <div class="mb-4">
        <input type="text" name="salary" placeholder="Annual Salary"
          value="<?= htmlspecialchars($job['salary'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="requirements" placeholder="Requirements"
          value="<?= htmlspecialchars($job['requirements'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="benefits" placeholder="Benefits"
          value="<?= htmlspecialchars($job['benefits'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <!-- Company Info -->
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-500">
        Company Info & Location
      </h2>

      <div class="mb-4">
        <input type="text" name="company" placeholder="Company Name"
          value="<?= htmlspecialchars($job['company'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="address" placeholder="Address"
          value="<?= htmlspecialchars($job['address'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="city" placeholder="City"
          value="<?= htmlspecialchars($job['city'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="state" placeholder="State"
          value="<?= htmlspecialchars($job['state'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="text" name="phone" placeholder="Phone"
          value="<?= htmlspecialchars($job['phone'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="email" name="email" placeholder="Email Address For Applications"
          value="<?= htmlspecialchars($job['email'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <div class="mb-4">
        <input type="tags" name="tags" placeholder="Tags (comma separated)"
          value="<?= htmlspecialchars($job['tags'] ?? '') ?>"
          class="w-full px-4 py-2 border rounded focus:outline-none" />
      </div>

      <!-- Submit & Cancel Buttons -->
      <button class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 my-3 rounded focus:outline-none">
        <?= $isEdit ? "Update" : "Save" ?>
      </button>

      <a href="/" class="block text-center w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded focus:outline-none">
        Cancel
      </a>
    </form>
  </div>
</section>

<?php if ($isEdit): ?>
<!-- JS will only run if editing -->
<script>
document.getElementById("jobForm").addEventListener("submit", async function(e) {
    e.preventDefault(); // Stop normal form submission

    const formData = new FormData(this);

    // Convert FormData to JSON, excluding _method
    const data = {};
    formData.forEach((value, key) => {
        if (key !== "_method") data[key] = value;
    });

    try {
        const res = await fetch("<?= '/jobs/' . $job['id'] . '/edit/' ?>", {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            window.location.href = "/jobs/details/<?= $job['id'] ?>";
        } else {
            const err = await res.json();
            alert(err.message || "Error updating job");
        }
    } catch (error) {
        alert("Network error: " + error.message);
    }
});

</script>
<?php endif; ?>

<?= loadPartial("footer") ?>
