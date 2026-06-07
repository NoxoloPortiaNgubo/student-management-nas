<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
</head>
<body>
    <h1>Register a New Student Profile</h1>
    <p><a href="{{ route('students.index') }}">← Back to Directory</a></p>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.store') }}" method="POST">
        @csrf <div style="margin-bottom: 10px;">
            <label for="user_id">User Account ID Reference:</label><br>
            <input type="number" id="user_id" name="user_id" value="{{ old('user_id') }}" required placeholder="e.g., 2">
            <small style="color: gray; display: block;">Link this profile to a valid numeric primary key inside the users table.</small>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="student_number">Student Number:</label><br>
            <input type="text" id="student_number" name="student_number" value="{{ old('student_number') }}" required placeholder="STU100234">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="phone">Phone Number (Optional):</label><br>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="date_of_birth">Date of Birth (Optional):</label><br>
            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
        </div>

        <button type="submit">Save Student Record</button>
    </form>
</body>
</html>