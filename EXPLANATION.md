# Brief Explanation (*Dataswitcher Tech Challenge*)

## Minor Revision Added

I had a little time to make a small refactor in order to simplify the workflow logic. Here are the changes that were implemented:

- Remove account update balance logic/method from model to a service class
- Refactor on Models
    - Constructor parameters now support singular values or an array
    - Model save method can handle single and array of values
- Added application name and version in .env file.

## Instructions

- Follow the instructions on the `README.md`
- Rename/Copy file `.env.example` to `.env`
- Fill with docker container values, like these :

```

    APP_NAME="DataSwitcher Challenge"
    APP_VERSION="0.2"

    CSV_FOLDER="data"

    DATABASE_SERVER="dsw_challenge_mongodb"
    DATABASE_NAME="dataswitcher"
    DATABASE_PORT=27017

```

- Tests will run like the instructions on the `README.md`, *(9 tests/26 assertions)*.

## Assumptions

- `settings.csv`, key `type` is for classify company type of data
- `journals.csv`, key `posted` (bool) do not control transactions import rule
- `list-accounts.csv`, key `Set` (bool) do not control accounts import rule
- There is no need to perserve the original id's/references

## Resume / Context

The challenge concluded when the data was successfully stored in MongoDB, adhering to the provided specifications *(I hope)*.

With over 15 years of experience in PHP development, my recent focus for the last 4-5 years has primarily revolved around the Laravel and Lumen frameworks and legacy projects refactory. Throughout this period, I've predominantly worked with relational databases like MySql, MariaDB, SQLServer, and SQLite. Consequently, delving into MongoDB posed an intriguing challenge for me. While I've had theoretical knowledge of MongoDB, this project provided my first practical hands-on experience with the technology. Even with testing, for the last > 2 years, I shifted to PEST instead of phpUnit.

As I progressed through the challenge, I became increasingly convinced that utilizing the core functionality of Laravel would have significantly reduced the time required for completion – probably less than half the time – and would have led to better code quality and overall structure. However, even though the challenge presented its own set of limitations and well-defined requirements, there were certain aspects that remained ambiguous and open-ended, potentially introducing a higher level of complexity during development.

## Technicalities

My initial step involved comprehending the problem at hand by thoroughly examining the provided information, including the data files housed in the data folder.

Establishing the infrastructure was a straightforward process, as Docker enabled me to ensure a uniform environment that met the project's requirements.

Initially, I decided to break down the problem into a series of smaller steps:

- Connect to the Database Engine.
- Create a migration approach to replicate the given database specifications.
- Utilize the `converter.php` file as the entry point to execute the code.
- Although I contemplated adopting a Test-Driven Development (TDD) approach, I opted to write tests incrementally as I developed the challenge, focusing on basic testing without employing a Mock System or a separate database for testing purposes.
- For the database schema, I devised a straightforward Migration system accompanied by an external JSON file to govern MongoDB collections and fields.
- My choice was to design a central CSV parser class, equipped with the concept of "model drivers" represented as traits. These traits were dedicated to each required model, and the parser itself was responsible solely for file and data operations. Thus, the core logic for generating entities from file data was encapsulated within the trait files. (My forward-thinking approach would involve creating new traits for future entities.)
- Each trait contained a predefined set of dynamic CSV headers to precisely target file data, all based on the provided files. Furthermore, these traits were tasked with bulk-saving entity data.
- The entities themselves are uncomplicated model classes imbued with simple logic. The one model that stood apart was the Account model. It featured a method designed to update account balances based on transaction lines. To streamline this operation, I leveraged MongoDB aggregations, employing projection and update operations rather than relying on PHP's array manipulation.

## Issues / Future Refactors

- While I incorporated a basic benchmark for the sample operation, it's essential to devise a strategy to optimize memory consumption, resource utilization, and execution time when handling extensive datasets. This requires meticulously monitoring each phase and measuring critical aspects to facilitate in-depth analysis, ultimately guiding informed decisions.

- To achieve this, an expansion of test coverage is imperative. This entails implementing a mock file system and dedicating a separate database specifically for testing purposes. Such measures not only bolster the reliability of the codebase but also ensure robustness in various scenarios.

- Furthermore, enhancing the log outputs for each step is paramount. A more verbose approach, offering pertinent information, is crucial for comprehending the intricacies of the process. This step aids in identifying potential bottlenecks and refining overall performance.

- The implementation of an Exception Workflow is also crucial for effective error handling. This empowers the system to gracefully handle unexpected scenarios and provides users with more informative error messages, contributing to a smoother user experience.

- Additionally, there's room for reimagining the structure of the CSV data processing. By designing a more sophisticated driver system, the solution can adapt seamlessly to a broader array of file scenarios and accommodate diverse entities. This adaptable architecture will contribute to the solution's versatility and extend its applicability beyond the current scope.

## Final Thoughts

In conclusion, embarking on this challenge has been an enriching experience for me. It reinforces the importance of a structured approach, continuous testing, and an eye toward optimization, thereby contributing to the development of robust and scalable solutions.
