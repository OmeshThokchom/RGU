# Contributing to RGU Portal

## Code of Conduct

This project and everyone participating in it is governed by the RGU Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to conduct@rgu.ac.in.

## Getting Started

### Prerequisites
1. PHP 7.4+
2. MySQL 5.7+
3. Composer
4. Git
5. Python 3.6+ (for development server)

### Development Setup
```bash
# Fork the repository
git clone https://github.com/your-username/rgu-portal.git
cd rgu-portal

# Create branch
git checkout -b feature/your-feature-name

# Install dependencies
composer install

# Configure environment
cp .env.example .env
cp config.example.php config.php

# Start development server
python server.py
```

## Development Process

### 1. Pick an Issue
- Check existing issues
- Create new issue if needed
- Get issue assigned to you
- Discuss approach if needed

### 2. Development Standards
Follow our [Development Standards](development_standards.md) guide for:
- Code style
- Documentation
- Testing
- Security measures

### 3. Making Changes
1. Create feature branch
2. Write code following standards
3. Add tests
4. Update documentation
5. Commit changes

### 4. Commit Guidelines

#### Commit Message Format
```
<type>(<scope>): <subject>

<body>

<footer>
```

#### Types
- feat: New feature
- fix: Bug fix
- docs: Documentation
- style: Formatting
- refactor: Code restructure
- test: Tests
- chore: Maintenance

#### Example
```
feat(students): add bulk import feature

- Add CSV import functionality
- Validate student data
- Handle duplicate entries

Closes #123
```

### 5. Pull Request Process
1. Update documentation
2. Run all tests
3. Update CHANGELOG.md
4. Create detailed PR description
5. Request code review
6. Address feedback

## Testing Guidelines

### Running Tests
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/StudentTest.php
```

### Writing Tests
```php
class StudentTest extends TestCase
{
    public function testStudentCreation()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $student = new Student($data);
        $this->assertEquals('John Doe', $student->getFullName());
    }
}
```

## Documentation

### Code Documentation
```php
/**
 * Process student registration
 *
 * @param array $data Student information
 * @param int $deptId Department identifier
 * @return Student|null
 * @throws ValidationException
 */
```

### Update Documentation
1. README.md for changes
2. API documentation
3. User guides
4. Installation steps

## Review Process

### Code Review Checklist
- [ ] Follows coding standards
- [ ] Tests included
- [ ] Documentation updated
- [ ] Security considered
- [ ] Performance checked

### Review Comments
- Be specific
- Be constructive
- Provide examples
- Explain reasoning

## Release Process

### Version Numbers
- MAJOR.MINOR.PATCH
- Major: Breaking changes
- Minor: New features
- Patch: Bug fixes

### Release Steps
1. Update version
2. Update CHANGELOG.md
3. Create release branch
4. Run final tests
5. Create release tag
6. Deploy to staging
7. Deploy to production

## Support

### Questions
- Check documentation
- Search existing issues
- Ask in discussions
- Contact support@rgu.ac.in

### Reporting Bugs
1. Check existing issues
2. Create detailed report
3. Include reproduction steps
4. Provide environment details
5. Add relevant logs

## Recognition

### Contributors
- Listed in CONTRIBUTORS.md
- Mentioned in release notes
- Acknowledged in documentation

### Contact
- Technical: tech@rgu.ac.in
- Support: support@rgu.ac.in
- Security: security@rgu.ac.in

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Version
Contributing Guide v1.0.0
Last Updated: 2025-05-08